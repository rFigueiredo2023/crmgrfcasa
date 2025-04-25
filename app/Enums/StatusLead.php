<?php

namespace App\Enums;

enum StatusLead: string
{
    case NOVO = 'novo';
    case FRIO = 'frio';
    case MORNO = 'morno';
    case QUENTE = 'quente';
    case CONVERTIDO = 'convertido';
    case PERDIDO = 'perdido';

    public function label(): string
    {
        return match($this) {
            self::NOVO => 'Novo',
            self::FRIO => 'Frio',
            self::MORNO => 'Morno',
            self::QUENTE => 'Quente',
            self::CONVERTIDO => 'Convertido',
            self::PERDIDO => 'Perdido',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NOVO => 'bg-primary',
            self::FRIO => 'bg-info',
            self::MORNO => 'bg-warning',
            self::QUENTE => 'bg-danger',
            self::CONVERTIDO => 'bg-success',
            self::PERDIDO => 'bg-secondary',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function($case) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
                'color' => $case->color()
            ];
        })->toArray();
    }
}
