<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Handphone;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\HandphoneResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HandphoneResource\RelationManagers;
use Filament\Tables\Filters\SelectFilter;

class HandphoneResource extends Resource
{
    protected static ?string $model = Handphone::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Details')

                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                    Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),

                    Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->required(),

                    Forms\Components\Repeater::make('photos')
                    ->relationship('photos')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                        ->required(),
                    ]),

                    Forms\Components\Repeater::make('capacities')
                    ->relationship('capacities')
                    ->schema([
                        Forms\Components\Select::make('capacity')
                        ->options([
                            '32 GB' => '32 GB',
                            '64 GB' => '64 GB',
                            '128 GB' => '128 GB',
                            '256 GB' => '256 GB',
                            '512 GB' => '512 GB',
                        ])
                        ->required(),
                    ]),
                ]),

                Fieldset::make('Additional')
                ->schema([
                    Forms\Components\Textarea::make('about')
                    ->required(),

                    Forms\Components\Select::make('is_popular')
                    ->options([
                        true => 'Popular',
                        false => 'Not Popular',
                    ])
                    ->required(),

                    Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),   

                    Forms\Components\Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                    Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->prefix('Qty'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable(),

                Tables\Columns\TextColumn::make('category.name'),

                Tables\Columns\ImageColumn::make('thumbnail'),

                Tables\Columns\TextColumn::make('stock'),

                Tables\Columns\TextColumn::make('price')
                ->money('IDR'),

                Tables\Columns\IconColumn::make('is_popular')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Popular'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                ->label('Category')
                ->relationship('category', 'name'),

                SelectFilter::make('brand_id')
                ->label('Brand')
                ->relationship('brand', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHandphones::route('/'),
            'create' => Pages\CreateHandphone::route('/create'),
            'edit' => Pages\EditHandphone::route('/{record}/edit'),
        ];
    }
}
