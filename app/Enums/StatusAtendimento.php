<?php

namespace App\Enums;

enum StatusAtendimento: string
{
    case ABERTO = 'aberto';
    case EM_ANDAMENTO = 'em_andamento';
    case CONCLUIDO = 'concluido';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::ABERTO => 'Aberto',
            self::EM_ANDAMENTO => 'Em Andamento',
            self::CONCLUIDO => 'Concluído',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            self::values(),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
