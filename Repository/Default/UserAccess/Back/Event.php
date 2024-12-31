<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Repository\Default\UserAccess\Back;

use Asset\Framework\Http\Request;
use Asset\Framework\Http\Response;
use Asset\Framework\Template\Form\FormSMG;
use Asset\Framework\Template\Render;
use Asset\Framework\ToolBox\Password;
use Asset\Framework\Trait\SingletonTrait;
use Entity\Default\User;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Random\RandomException;

/**
 * Class that handles: Events of/over User Access
 *
 * @package Repository\Default\UserAccess\Back;
 */
class Event
{

    use SingletonTrait;

    private const array VALIDATION_RULES
        = [
            'register-first-name'       => ['required' => true, 'message' => '{{register-first-name-smg}}'],
            'register-last-name'        => ['required' => true, 'message' => '{{register-last-name-smg}}'],
            'register-email'            => ['required' => true, 'message' => '{{register-email-smg-0}}'],
            'register-re-email'         => ['required' => true, 'message' => '{{register-re-email-smg-0}}'],
            'register-password'         => ['required' => true, 'message' => '{{register-password-smg-0}}'],
            'register-agree-conditions' => [
                'required' => true,
                'value'    => true,
                'message'  => '{{register-agree-conditions-smg}}',
            ],
        ];

    /**
     * @var Render
     */
    private Render $render;

    /**
     * @var bool
     */
    private bool $event_exists = false;

    /**
     * @var string
     */
    private string $event = '';

    /**
     * @var Main
     */
    private Main $main;

    /**
     * @var Response
     */
    private Response $response;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var array
     */
    private array $post;

    /**
     * Event constructor.
     */
    public function __construct(Main $main)
    {
        if (!empty($_POST)) {
            $this->initializeEvent($main);
        }
    }

    /**
     * @param Main $main
     * @return void
     */
    private function initializeEvent(Main $main): void
    {

        $this->setEventExists(true)
            ->setEvent($_POST['event'])
            ->setMain($main)
            ->setRender(Render::getInstance())
            ->setResponse(Response::getInstance())
            ->setRequest(Request::getInstance())
            ->setPost($this->getRequest()->getPost());

        $this->getResponse()->setEvent($this);
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @param array $post
     * @return Event
     */
    public function setPost(array $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return Event
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function eventHandler(): self
    {
        if ($this->isEventExists()) {
            $this->eventListener();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEventExists(): bool
    {
        return $this->event_exists;
    }

    /**
     * @param bool $event_exists
     * @return $this
     */
    public function setEventExists(bool $event_exists): self
    {
        $this->event_exists = $event_exists;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function eventListener(): self
    {
        if (method_exists($this, $this->getEvent())) {
            $this->{$this->event}();
        } else {
            //throw new Exception('Event not found');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     * @return $this
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return void
     */
    private function login(): void
    {
        ex_c('Test de login');
    }

    /**
     * @return Response
     * @throws RandomException
     */
    private function register(): Response
    {

        $validationErrors = $this->validateRegistrationData();

        if (!empty($validationErrors)) {
            return $this->handleValidationErrors($validationErrors);
        }

        return $this->createUserAccount();

        /*
        $user = User::getInstance();

        $post = $this->getPost();

        $smg = $this->getMain()->getSmg();

        $error = [];

        if ($post['register-first-name'] === '') {

            $error[] = [
                'field'  => 'register-first-name-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-first-name-smg', 'invalid'))->getLastMessage(),
            ];

        }

        if ($post['register-last-name'] === '') {

            $error[] = [
                'field'  => 'register-last-name-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-last-name-smg', 'invalid'))->getLastMessage(),
            ];
        }

        if ($post['register-email'] === '') {
            $error[] = [
                'field'  => 'register-email-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-email-smg-0', 'invalid'))->getLastMessage(),
            ];
        }

        if ($post['register-re-email'] === '') {
            $error[] = [
                'field'  => 'register-re-email-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-re-email-smg-0', 'invalid'))->getLastMessage(),
            ];
        }

        if ($post['register-re-email'] !== $post['register-email']) {
            $error[] = [
                'field'  => 'register-email-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-email-smg-1', 'invalid'))->getLastMessage(),
            ];
            $error[] = [
                'field'  => 'register-re-email-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-re-email-smg-1', 'invalid'))->getLastMessage(),
            ];
        }

        if ($post['register-password'] === '') {
            $error[] = [
                'field'  => 'register-password-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-password-smg-0', 'invalid'))->getLastMessage(),
            ];
        }

        $pass_eval = Password::quickCheck($post['register-password']);

        if (!empty($pass_eval)) {
            $error[] = [
                'field'  => 'register-password-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG(implode(NL, $pass_eval), 'invalid'))->getLastMessage(),
            ];
        }

        if ($post['register-agree-conditions'] !== true) {
            $error[] = [
                'field'  => 'register-agree-conditions-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG(
                    $this->feedbackSMG('register-agree-conditions-smg', 'invalid')
                )->getLastMessage(),
            ];
        }

        if ($user->emailExists($post['register-email'])) {
            $error[] = [
                'field'  => 'register-email-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-email-smg-2', 'invalid'))->getLastMessage(),
            ];
        }

        if (!empty($error)) {
            $error[] = [
                'field'  => 'register-head-smg',
                'status' => 'error',
                'smg'    => $smg->setSMG($this->feedbackSMG('register-smg-0', 'warning'))->getLastMessage(),
            ];

            return $this->getResponse()
                ->setContent($error)
                ->setOutputFormat('json')
                ->setIsError(true)
                ->setShow(true);

        } else {

            $credentials = [
                'first_name' => $post['register-first-name'],
                'last_name'  => $post['register-last-name'],
                'email'      => $post['register-email'],
                'password'   => password_hash($post['register-password'], PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $result = $user->createAccount($credentials);

            if ($result) {
                $out[] = [
                    'field'  => 'register-head-smg',
                    'status' => 'success',
                    'smg'    => $smg->setSMG($this->feedbackSMG('register-email-success', 'success'))->getLastMessage(),
                ];

                return $this->getResponse()
                    ->setContent($out)
                    ->setOutputFormat('json')
                    ->setIsError(false)
                    ->setRefresh(true)
                    ->setShow(true);

            } else {

                $error[] = [
                    'field'  => 'register-head-smg',
                    'status' => 'error',
                    'smg'    => $smg->setSMG($this->feedbackSMG('register-email-fail', 'danger'))->getLastMessage(),
                ];

                return $this->getResponse()
                    ->setContent($error)
                    ->setOutputFormat('json')
                    ->setIsError(true)
                    ->setShow(true);
            }

        }
        */


    }

    /**
     * @return array
     */
    private function validateRegistrationData(): array
    {
        $errors = [];
        $post   = $this->getPost();

        foreach (self::VALIDATION_RULES as $field => $rules) {
            if ($rules['required'] && empty($post[$field])) {
                $errors[] = $this->createErrorMessage($rules['message'], $field.'-smg');
            }
        }

        if ($post['register-re-email'] !== $post['register-email']) {
            $errors[] = $this->createErrorMessage('{{register-email-smg-1}}', 'register-email-smg');
            $errors[] = $this->createErrorMessage('{{register-re-email-smg-1}}', 'register-re-email-smg');
        }

        $passwordValidation = Password::quickCheck($post['register-password']);
        if (!empty($passwordValidation)) {
            $errors[] = $this->createErrorMessage(implode(NL, $passwordValidation), 'register-password-smg');
        }

        if (User::getInstance()->emailExists($post['register-email'])) {
            $errors[] = $this->createErrorMessage('{{register-email-smg-2}}', 'register-email-smg');
        }

        return $errors;
    }

    /**
     * @param string $messageKey
     * @param string $field
     * @return array
     */
    private function createErrorMessage(string $messageKey, string $field): array
    {
        return [
            'field'  => $field,
            'status' => 'error',
            'smg'    => $this->getMain()->getSmg()->setSMG(
                $this->feedbackSMG($messageKey, 'invalid')
            )->getLastMessage(),
        ];
    }

    /**
     * @return Main
     */
    public function getMain(): Main
    {
        return $this->main;
    }

    /**
     * @param Main $main
     * @return $this
     */
    public function setMain(Main $main): self
    {
        $this->main = $main;

        return $this;
    }

    /**
     * @param string $token
     * @param string $status
     * @return array
     */
    private function feedbackSMG(string $token, string $status): array
    {


        $content = $this->getRender()
            ->setDic($this->getMain()->getDic())
            ->setContent($token)
            ->getTranslateContent();

        $cite = $this->getRender()
            ->setDic($this->getMain()->getDic())
            ->setContent('{{form-validation}}')
            ->getTranslateContent();

        $footer = $this->getRender()
            ->setDic($this->getMain()->getDic())
            ->setContent('{{system-message}}')
            ->getTranslateContent();


        if (!array_key_exists($status, FormSMG::getTypes())) {
            $status = 'default';
        }

        $messageConfig = [
            'type'    => $status,
            'content' => $content,
            'in'      => 'inline',
        ];

        if (!in_array($status, ['valid', 'invalid'])) {
            $messageConfig['footer'] = $footer;
            $messageConfig['cite']   = $cite;
        }

        return $messageConfig;
    }

    /**
     * @return Render
     */
    public function getRender(): Render
    {
        return $this->render;
    }

    /**
     * @param Render $render
     * @return $this
     */
    public function setRender(Render $render): self
    {
        $this->render = $render;

        return $this;
    }

    /**
     * @param array $errors
     * @return Response
     */
    private function handleValidationErrors(array $errors): Response
    {
        $errors[] = [
            'field'  => 'register-head-smg',
            'status' => 'error',
            'smg'    => $this->getMain()->getSmg()->setSMG(
                $this->feedbackSMG('{{register-smg-0}}', 'warning')
            )->getLastMessage(),
        ];

        return $this->getResponse()
            ->setContent($errors)
            ->setOutputFormat('json')
            ->setIsError(true)
            ->setShow(true);
    }

    /**
     * @return Response
     * @throws RandomException
     */
    private function createUserAccount(): Response
    {
        $credentials = [
            'first_name' => $this->post['register-first-name'],
            'last_name'  => $this->post['register-last-name'],
            'email'      => $this->post['register-email'],
            'password'   => password_hash($this->post['register-password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $success = User::getInstance()->createAccount($credentials);

        return $success
            ? $this->createSuccessResponse()
            : $this->createFailureResponse();
    }

    /**
     * @return Response
     */
    private function createSuccessResponse(): Response
    {
        return $this->getResponse()
            ->setContent([
                [
                    'field'  => 'register-head-smg',
                    'status' => 'success',
                    'smg'    => $this->getMain()->getSmg()->setSMG(
                        $this->feedbackSMG('{{register-email-success}}', 'success')
                    )->getLastMessage(),
                ],
            ])
            ->setOutputFormat('json')
            ->setIsError(false)
            ->setRefresh(true)
            ->setShow(true);
    }

    /**
     * @return Response
     */
    private function createFailureResponse(): Response
    {
        return $this->getResponse()
            ->setContent([
                [
                    'field'  => 'register-head-smg',
                    'status' => 'error',
                    'smg'    => $this->getMain()->getSmg()->setSMG(
                        $this->feedbackSMG('{{register-email-fail}}', 'danger')
                    )->getLastMessage(),
                ],
            ])
            ->setOutputFormat('json')
            ->setIsError(true)
            ->setShow(true);
    }

    /**
     * @return void
     */
    #[NoReturn] private function loginWithGoogle(): void
    {
        ex_c('Test de loginWithGoogle');
    }

    /**
     * @return void
     */
    #[NoReturn] private function loginWithFacebook(): void
    {
        ex_c('Test de loginWithFacebook');
    }
}