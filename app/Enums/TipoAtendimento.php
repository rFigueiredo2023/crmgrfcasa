<?php

namespace App\Enums;

enum TipoAtendimento: string
{
    case TELEFONE = 'telefone';
    case EMAIL = 'email';
    case WHATSAPP = 'whatsapp';
    case PRESENCIAL = 'presencial';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::TELEFONE => 'Telefone',
            self::EMAIL => 'E-mail',
            self::WHATSAPP => 'WhatsApp',
            self::PRESENCIAL => 'Presencial',
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
