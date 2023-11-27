<?php

namespace ext;

use DateTime;

class Sanitize {

    public static function sanitizeString(string $input): string
    {
        return (preg_match('/^[a-zA-Z0-9_]+$/', $input) ? $input : 'injection');
    }

    public static function sanitizeDate(string $input): string
    {
        $date = DateTime::createFromFormat('Y-m-d', $input);

        if ($date && $date->format('Y-m-d') === $input) {
            return $input;
        }

        return '0000-00-00';
    }

    public static function sanitizeEmail(string $email): string
    {
        if (!$email) {
            return '';
        }

        $email = trim($email);
        $email = trim($email, '.');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return '';
        }

        return $email;
    }

    public static function sanitizePhone(string $phone): string
    {
        $phone = str_replace(' ', '', $phone);
        return (preg_match('/^[0-9]+$/', $phone) ? $phone : '');
    }

    public static function sanitizeNumber(string $number): string
    {
        return (preg_match('/^[0-9]+$/', $number) ? $number : '');
    }

    public static function sanitizeDecimal(string $number): string
    {
        return filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    public static function sanitizeRequest(array $requestData): array
    {
        $fields = [
            'startdatum', 'start', 'enddatum', 'ende', 'birthdate',
            'email', 'email_privat', 'telefon', 'handy', 'country',
            'vorname', 'nachname', 'strasse', 'plz', 'ort', 'nr_land',
            'geschlecht', 'kursnummer', 'kursthema', 'inhalt', 'nr_dozent',
            'dauer', 'nr_teilnehmer', 'nr_kurs', 'note', 'firma', 'beruf',
            'nr_lehrbetrieb'
        ];

        foreach ($fields as $field) {
            if (isset($requestData[$field])) {
                $requestData[$field] = match ($field) {
                    'email', 'email_privat' => Sanitize::sanitizeEmail($requestData[$field]),
                    'telefon', 'handy' => Sanitize::sanitizePhone($requestData[$field]),
                    'plz', 'nr_land', 'nr_dozent', 'dauer', 'nr_teilnehmer', 'nr_kurs', 'nr_lehrbetrieb' => Sanitize::sanitizeNumber($requestData[$field]),
                    'startdatum', 'start', 'enddatum', 'ende', 'birthdate' => Sanitize::sanitizeDate($requestData[$field]),
                    'note' => Sanitize::sanitizeDecimal($requestData[$field]),
                    default => Sanitize::sanitizeString($requestData[$field])
                };
            }
        }
        return $requestData;
    }
}