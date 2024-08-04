<?php

declare(strict_types=1);

namespace Asset\Framework\Core;

use JetBrains\PhpStorm\NoReturn;

/**
 * Class Request
 * A simple ...
 */
class Request
{
    /**
     * @var Request|null Singleton instance of the Request.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Request.
     *
     * @return Request The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public function CleanSuperGlobal(): void
    {
        if (isset($_POST) && empty($_POST)) {
            unset($_POST);
        } else {
            $_POST = $this->evaluateSuperGlobal($_POST);
        }
        if (isset($_GET) && empty($_GET)) {
            unset($_GET);
        } else {
            $_GET = $this->evaluateSuperGlobal($_GET);
        }
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    private function evaluateSuperGlobal($data): mixed
    {
        $result = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (Variable::getInstance()->isJson($value)) {
                    $jsonObj      = json_decode($value, true);
                    $result[$key] = $this->evaluateSuperGlobal($jsonObj);
                } elseif (is_array($value)) {
                    $result[$key] = $this->evaluateSuperGlobal($value);
                } else {
                    if (strcasecmp($value, "true") === 0 || strcasecmp($value, "false") === 0) {
                        $result[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    } elseif (is_numeric($value)) {
                        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                            $result[$key] = (int) $value;
                        } elseif (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
                            $result[$key] = (float) $value;
                        } else {
                            $result[$key] = $value;
                        }
                    } else {
                        $result[$key] = self::sanitizeInput($value);
                    }
                }
            }
        } else {
            $result = $data;
        }

        return $result;
    }

    /**
     * @param $input
     *
     * @return string
     */
    public static function sanitizeInput($input): string
    {
        $input = strip_tags($input);
        $input = addslashes($input);
        $input = htmlentities($input, ENT_QUOTES, 'UTF-8');
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return trim($input);
    }

    #[NoReturn] public function redirect(string $location): void
    {
        header("Location: " . $location);
        exit;
    }


}