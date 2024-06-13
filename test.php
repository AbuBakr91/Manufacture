<?php

interface UserSettingServiceI {
    public function requestSettingChange(int $userId, string $setting, string $value): void;
    public function confirmSettingChange(int $userId, string $setting, string $value, string $code): void;
}

interface ConfirmationCodeServiceI {
    public function generateCode(): string;
    public function verifyCode(string $code): bool;
}

interface NotificationServiceI {
    public function sendNotification(int $userId, string $message): void;
}

class ConfirmationCodeService implements ConfirmationCodeServiceI {

    public function generateCode(): string
    {
        // TODO: генерируем код.
    }

    public function verifyCode(string $code): bool
    {
        // TODO: метод для верификации.
    }
}

class NotificationService implements NotificationServiceI {

    public function sendNotification(int $userId, string $message): void
    {
        // TODO: метод потавки уведомления с кодом в зависимости от настроек пользователя
        // можно сделать таблицу notifications и с полями user_id и типом уведомления (смс/телеграм/email)
    }
}

class UserSettingService implements UserSettingServiceI {

    public function requestSettingChange(int $userId, string $setting, string $value): void
    {
        // TODO: добавление в таблицу user_settings новую настройку в поле requested_value после подтверждения переходит в current_value
    }

    public function confirmSettingChange(int $userId, string $setting, string $value, string $code): void
    {
        // TODO: после подтвеждения переносим requested_value в current_value и очищаем
    }
}

class UserSettingsController {

    public function __construct(
        private UserSettingServiceI $userSettingService,
        private ConfirmationCodeServiceI $confirmationCodeService,
        private NotificationServiceI $notificationService
    ) {}

    public function requestSettingChange(int $userId, string $setting, string $value): string
    {
        $this->userSettingService->requestSettingChange($userId, $setting, $value);

        $code = $this->confirmationCodeService->generateCode();

        $message = "Ваш код подтверждения: " . $code;
        $this->notificationService->sendNotification($userId, $message);

        return "Код подтверждения отправлен!";
    }

    public function confirmSettingChange(int $userId, string $setting, string $value, string $code): string
    {
        if (!$this->confirmationCodeService->verifyCode($code)) {
            return "Неправильный код подтверждения";
        }

        $this->userSettingService->confirmSettingChange($userId, $setting, $value, $code);

        return "Настройки успешно обновлены!";
    }
}
