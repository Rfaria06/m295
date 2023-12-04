<?php
namespace ext;

use DateTime;

/**
 * Sanitizer class
 * @noinspection PhpUnused
 */
class Sanitize {

    /**
     * @param string $input the string to be sanitized
     * @return string the sanitized string
     * If an injection is detected, the application stops and returns an error message
     */
    public static function sanitizeString(string $input): string
    {
        $sanitized = (preg_match('/^[a-zA-Z0-9_]+$/', $input) ? $input : 'injection');
        if ($sanitized === 'injection') {
            header('Content-Type: application/json');
            http_response_code(400);
            die(json_encode(['error' => 'Possible injection detected']));
        }
        return $sanitized;
    }

    /**
     * @param string $input the date to be sanitized
     * @return string the sanitized date
     * Tries to parse the $input as date, if that does not work, the standard date of 0000-00-00 is returned
     */
    private static function sanitizeDate(string $input): string
    {
        $date = DateTime::createFromFormat('Y-m-d', $input);

        if ($date && $date->format('Y-m-d') === $input) {
            return $input;
        }

        return '0000-00-00';
    }

    /**
     * @param string $email the email to be sanitized
     * @return string the sanitized email
     * trims and filters the $input as email. If that fais, return empty string
     */
    private static function sanitizeEmail(string $email): string
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

    /**
     * @param string $phone the phone number to be sanitized
     * @return string the sanitized phone number or an empty string
     */
    private static function sanitizePhone(string $phone): string
    {
        $phone = str_replace(' ', '', $phone);
        return (preg_match('/^[0-9]+$/', $phone) ? $phone : '');
    }

    /**
     * @param string $number number to be sanitized
     * @return string sanitized number
     * Difference to sanitizePhone -> sanitizePhone deletes spaces, this one leaves the spaces
     */
    private static function sanitizeNumber(string $number): string
    {
        return (preg_match('/^[0-9]+$/', $number) ? $number : '');
    }

    /**
     * @param string $number the decimal to be sanitized
     * @return string the sanitized decimal
     */
    private static function sanitizeDecimal(string $number): string
    {
        return filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * @param array $requestData the HTTP Request
     * @return array the sanitized HTTP Request
     * This function utilises this classes functions to sanitize the entire HTTP Request array
     */
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