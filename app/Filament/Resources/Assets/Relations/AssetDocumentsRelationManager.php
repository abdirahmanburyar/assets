<?php

namespace App\Filament\Resources\Assets\Relations;

use App\Models\AssetDocument;
use App\Http\Requests\AssetDocumentRequest;
use App\Traits\HasPermissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class AssetDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'file_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label('Document File')
                    ->required()
                    ->maxSize(512000) // 500MB in KB
                    ->directory('asset-documents')
                    ->disk('public')
                    ->preserveFilenames()
                    ->downloadable()
                    ->previewable()
                    ->storeFileNamesIn('original_filename')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif'])
                    ->validationMessages([
                        'accepted' => 'The file must be a PDF or image file (pdf, jpg, jpeg, png, gif).',
                    ]),
                
                TextInput::make('file_name')
                    ->label('File Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('file_type')
                    ->label('Type')
                    ->getStateUsing(function (AssetDocument $record): string {
                        $extension = strtolower(pathinfo($record->file_path, PATHINFO_EXTENSION));
                        if ($extension === 'pdf') {
                            return '📄 PDF';
                        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            return '🖼️ Image';
                        } else {
                            return '📁 File';
                        }
                    })
                    ->sortable(),
                
                TextColumn::make('file_size')
                    ->label('Size')
                    ->getStateUsing(fn (AssetDocument $record): string => $record->file_size_formatted)
                    ->sortable(),
                
                TextColumn::make('uploaded_by')
                    ->label('Uploaded By')
                    ->getStateUsing(fn (AssetDocument $record): string => $record->uploader?->name ?? 'Unknown')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->visible(fn (): bool => HasPermissions::userCan('asset_document.download'))
                        ->url(fn (AssetDocument $record): string => $record->download_url),
                    
                    EditAction::make()
                        ->visible(fn (): bool => HasPermissions::userCan('asset_document.edit')),
                    
                    DeleteAction::make()
                        ->visible(fn (): bool => HasPermissions::userCan('asset_document.delete')),
                ]),
            ])
            ->bulkActions([
                // Bulk actions will be handled by Filament v4 automatically
            ])
            ->headerActions([
                Action::make('upload_document')
                    ->label('Upload Document')
                    ->icon('heroicon-o-plus')
                    ->visible(fn (): bool => HasPermissions::userCan('asset_document.upload'))
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file_path')
                            ->label('Document File')
                            ->required()
                            ->maxSize(512000) // 500MB in KB
                            ->directory('asset-documents')
                            ->disk('public')
                            ->preserveFilenames()
                            ->downloadable()
                            ->previewable()
                            ->storeFileNamesIn('original_filename')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif'])
                            ->validationMessages([
                                'accepted' => 'The file must be a PDF or image file (pdf, jpg, jpeg, png, gif).',
                            ]),
                        
                        TextInput::make('file_name')
                            ->label('File Name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        try {
                            \Log::info('Asset Document Upload Data:', $data);
                            \Log::info('Data types:', [
                                'file_path_type' => gettype($data['file_path']),
                                'file_path_value' => $data['file_path'],
                                'file_name_type' => gettype($data['file_name']),
                                'file_name_value' => $data['file_name'],
                            ]);
                            
                            $filePath = is_array($data['file_path']) ? $data['file_path'][0] : $data['file_path'];
                            
                            \Log::info('Processed file path:', ['file_path' => $filePath]);
                            
                            if (empty($filePath)) {
                                throw new \Exception('No file was uploaded.');
                            }
                            
                            if (str_contains($filePath, 'livewire-tmp')) {
                                \Log::warning('File is still in temp directory, waiting for processing...');
                                sleep(1);
                                if (str_contains($filePath, 'livewire-tmp')) {
                                    throw new \Exception('File upload is still processing. Please wait and try again.');
                                }
                            }
                            
                            \Log::info('Checking file existence:', [
                                'file_path' => $filePath,
                                'storage_disk' => 'public',
                                'full_path' => Storage::disk('public')->path($filePath)
                            ]);
                            
                            if (!Storage::disk('public')->exists($filePath)) {
                                $filesInDirectory = Storage::disk('public')->allFiles('asset-documents');
                                \Log::info('Files in asset-documents directory:', $filesInDirectory);
                                $allFiles = Storage::disk('public')->allFiles();
                                \Log::info('All files in public storage:', $allFiles);
                                $tempFiles = Storage::disk('public')->allFiles('livewire-tmp');
                                \Log::info('Files in temp directory:', $tempFiles);
                                
                                throw new \Exception('Uploaded file not found. Please try again.');
                            }
                            
                            $request = new AssetDocumentRequest();
                            $request->merge([
                                'file_path' => $filePath,
                                'file_name' => $data['file_name']
                            ]);
                            
                            $validator = validator($request->all(), $request->rules(), $request->messages());
                            
                            if ($validator->fails()) {
                                \Log::error('Asset Document Validation Failed:', $validator->errors()->toArray());
                                $firstError = $validator->errors()->first();
                                throw new \Exception($firstError);
                            }
                            
                            $this->getOwnerRecord()->documents()->create([
                                'file_path' => $filePath,
                                'file_name' => $data['file_name'],
                                'uploaded_by' => auth()->id(),
                            ]);
                            
                            \Log::info('Asset Document uploaded successfully:', [
                                'asset_id' => $this->getOwnerRecord()->id,
                                'file_path' => $filePath,
                                'file_name' => $data['file_name']
                            ]);
                            
                        } catch (\Exception $e) {
                            \Log::error('Asset Document Upload Failed:', [
                                'error' => $e->getMessage(),
                                'data' => $data,
                                'asset_id' => $this->getOwnerRecord()->id ?? 'unknown'
                            ]);
                            throw $e;
                        }
                    }),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['uploader']));
    }
}
