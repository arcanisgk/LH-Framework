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

namespace Asset\Framework\Core;

use Asset\Framework\Template\MailComposer;
use Asset\Framework\Trait\SingletonTrait;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Random\RandomException;

/**
 * Class that handles: Email Sender
 *
 * @package Asset\Framework\Core;
 */
class Mailer
{

    use SingletonTrait;

    /**
     * @var PHPMailer
     */
    private PHPMailer $mailer;

    /**
     * @var string
     */
    private string $defaultFromAddress;

    /**
     * @var string
     */
    private string $defaultFromName = 'LH Framework';

    /**
     * @var string
     */
    private string $privateKeyPath;

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        $this->defaultFromAddress = CONFIG->mail->mail1->getMailUser();
        $this->mailer             = new PHPMailer(true);
        $this->privateKeyPath     = Files::getInstance()->getAbsolutePath(
            implode(DS, [PD, 'Asset', 'resource', 'keyring', 'private.pem'])
        );
        $this->configure();
    }


    /**
     * @return void
     * @throws Exception|RandomException
     */
    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->SMTPDebug             = CONFIG->mail->mail1->getMailDebug();
        $this->mailer->Host                  = CONFIG->mail->mail1->getMailHost();
        $this->mailer->MessageID             = sprintf(
            '<%s@%s>',
            bin2hex(random_bytes(16)),
            CONFIG->app->host->getDomain()
        );
        $this->mailer->SMTPAuth              = CONFIG->mail->mail1->getMailAuthentication();
        $this->mailer->Username              = $this->defaultFromAddress;
        $this->mailer->Password              = CONFIG->mail->mail1->getMailPassword();
        $this->mailer->SMTPSecure            = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->SMTPOptions           = [
            'ssl' => [
                'crypto_method'     => STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT,
                'verify_peer'       => CONFIG->mail->mail1->getMailVerify(),
                'verify_peer_name'  => CONFIG->mail->mail1->getMailVerifyPeerName(),
                'allow_self_signed' => CONFIG->mail->mail1->getMailSelfSigned(),
            ],
        ];
        $this->mailer->Port                  = CONFIG->mail->mail1->getMailPort();
        $this->mailer->DKIM_domain           = CONFIG->app->host->getDomain();
        $this->mailer->DKIM_private          = $this->privateKeyPath;
        $this->mailer->DKIM_selector         = CONFIG->mail->mail1->getMailDkimSign();
        $this->mailer->DKIM_passphrase       = CONFIG->mail->mail1->getMailDkimPassphrase();
        $this->mailer->DKIM_identity         = $this->defaultFromAddress;
        $this->mailer->DKIM_copyHeaderFields = CONFIG->mail->mail1->getMailDkimCopyHeaderFields();
        $this->mailer->DKIM_extraHeaders     = ['List-Unsubscribe', 'List-Help'];
        $this->mailer->CharSet               = 'UTF-8';
        $this->mailer->Encoding              = PHPMailer::ENCODING_8BIT;
        $this->mailer->XMailer               = 'LH Framework Mailer ('.
            CONFIG->app->host->getProtocol().'://'.
            CONFIG->app->host->getDomain().')';
        $this->mailer->addCustomHeader('X-Mailer-Version', 'LH-2.0');
        $this->mailer->addCustomHeader('List-Unsubscribe', 'List-Unsubscribe=One-Click');
        $this->mailer->addCustomHeader('Precedence', 'bulk');
        $this->mailer->addCustomHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
        foreach (SecurityPolicies::getHumanitarianHeaders('mail') as $key => $value) {
            $this->mailer->addCustomHeader($key, $value);
        }
    }


    /**
     * @param string $to
     * @param string $subject
     * @param array $emailData
     * @param bool $isHtml
     * @param array $attachments
     * @param string $fromName
     * @param string|null $plainTextBody
     * @return bool
     * @throws \Exception
     */
    public function send(
        string $to,
        string $subject,
        array $emailData,
        bool $isHtml = true,
        array $attachments = [],
        string $fromName = 'LH Framework',
        string $plainTextBody = null
    ): bool {
        try {
            $htmlBody = MailComposer::getInstance()->buildEmail($emailData);
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->setFrom($this->defaultFromAddress, $fromName ?? $this->defaultFromName);
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML($isHtml);
            $this->mailer->Body    = $htmlBody;
            $this->mailer->AltBody = $plainTextBody ?? $this->convertToPlainText($htmlBody);
            foreach ($attachments as $attachment) {
                if (is_array($attachment) && isset($attachment['path'], $attachment['name'])) {
                    $this->mailer->addAttachment(
                        $attachment['path'],
                        $attachment['name'],
                        PHPMailer::ENCODING_BASE64,
                        $this->getMimeType($attachment['path'])
                    );
                } elseif (is_string($attachment)) {
                    $this->mailer->addAttachment($attachment);
                }
            }

            return $this->mailer->send();
        } catch (Exception $e) {
            // Aquí podrías implementar tu sistema de logging
            return false;
        }
    }

    /**
     * @param string $html
     * @return string
     */
    private function convertToPlainText(string $html): string
    {
        // Convert HTML to plain text
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

        $text = strip_tags($html);
        $text = html_entity_decode($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/[\r\n]{2,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * @param string $filepath
     * @return string
     */
    private function getMimeType(string $filepath): string
    {
        return mime_content_type($filepath) ?: 'application/octet-stream';
    }

    /**
     * @return PHPMailer
     * @throws Exception
     * @throws RandomException
     */
    public function __debugInfo()
    {
        $this->configure();

        return $this->mailer;
    }
}