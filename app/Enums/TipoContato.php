<?php

namespace App\Enums;

enum TipoContato: string
{
    case TELEFONE = 'telefone';
    case EMAIL = 'email';
    case WHATSAPP = 'whatsapp';
    case PRESENCIAL = 'presencial';
    case VIDEOCONFERENCIA = 'videoconferencia';
    case OUTRO = 'outro';

    public function label(): string
    {
        return match($this) {
            self::TELEFONE => 'Telefone',
            self::EMAIL => 'E-mail',
            self::WHATSAPP => 'WhatsApp',
            self::PRESENCIAL => 'Presencial',
            self::VIDEOCONFERENCIA => 'Videoconferência',
            self::OUTRO => 'Outro',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TELEFONE => 'ti ti-phone',
            self::EMAIL => 'ti ti-mail',
            self::WHATSAPP => 'ti ti-brand-whatsapp',
            self::PRESENCIAL => 'ti ti-users',
            self::VIDEOCONFERENCIA => 'ti ti-video',
            self::OUTRO => 'ti ti-note',
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
                'icon' => $case->icon(),
            ];
        })->toArray();
    }
}
