<?php
namespace Skrill\Configs;
use Skrill\Constants\Plugin;
use Skrill\Methods\PchPaymentMethod;
use Skrill\Methods\AccPaymentMethod;
use Skrill\Methods\AciPaymentMethod;
use Skrill\Methods\AdbPaymentMethod;
use Skrill\Methods\AliPaymentMethod;
use Skrill\Methods\AobPaymentMethod;
use Skrill\Methods\ApmPaymentMethod;
use Skrill\Methods\AupPaymentMethod;
use Skrill\Methods\CsiPaymentMethod;
use Skrill\Methods\DnkPaymentMethod;
use Skrill\Methods\EbtPaymentMethod;
use Skrill\Methods\EpyPaymentMethod;
use Skrill\Methods\GcbPaymentMethod;
use Skrill\Methods\GirPaymentMethod;
use Skrill\Methods\IdlPaymentMethod;
use Skrill\Methods\MaePaymentMethod;
use Skrill\Methods\MscPaymentMethod;
use Skrill\Methods\NpyPaymentMethod;
use Skrill\Methods\NtlPaymentMethod;
use Skrill\Methods\ObtPaymentMethod;
use Skrill\Methods\PliPaymentMethod;
use Skrill\Methods\PscPaymentMethod;
use Skrill\Methods\PspPaymentMethod;
use Skrill\Methods\PwyPaymentMethod;
use Skrill\Methods\SftPaymentMethod;
use Skrill\Methods\VsaPaymentMethod;
use Skrill\Methods\WltPaymentMethod;

class MethodConfig extends BaseConfig implements MethodConfigContract
{
    const ARRAY_KEY_CONFIG_KEY = 'config_key';
    const ARRAY_KEY_DEFAULT_NAME = 'default_name';
    const ARRAY_KEY_KEY = 'key';

    const NO_CONFIG_KEY_FOUND = 'no_config_key_found';
    const NO_DEFAULT_NAME_FOUND = 'no_default_name_found';
    const NO_KEY_FOUND = 'no_key_found';

    /**
     * @var array
     */
    public static $paymentMethods = [
        PchPaymentMethod::class => [
            self::ARRAY_KEY_KEY => PchPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => PchPaymentMethod::DEFAULT_NAME
        ],
        AccPaymentMethod::class => [
            self::ARRAY_KEY_KEY => AccPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => AccPaymentMethod::DEFAULT_NAME
        ],
        AciPaymentMethod::class => [
            self::ARRAY_KEY_KEY => AciPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => AciPaymentMethod::DEFAULT_NAME
        ],
        AdbPaymentMethod::class => [
            self::ARRAY_KEY_KEY => AdbPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => AdbPaymentMethod::DEFAULT_NAME
        ],
        AliPaymentMethod::class => [
            self::ARRAY_KEY_KEY => AliPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => AliPaymentMethod::DEFAULT_NAME
        ],
        AobPaymentMethod::class => [
            self::ARRAY_KEY_KEY => AobPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => AobPaymentMethod::DEFAULT_NAME
        ],
        ApmPaymentMethod::class => [
            self::ARRAY_KEY_KEY => ApmPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => ApmPaymentMethod::DEFAULT_NAME
        ],
        AupPaymentMethod::class => [
            self::ARRAY_KEY_KEY => AupPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => AupPaymentMethod::DEFAULT_NAME
        ],
        CsiPaymentMethod::class => [
            self::ARRAY_KEY_KEY => CsiPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => CsiPaymentMethod::DEFAULT_NAME
        ],
        DnkPaymentMethod::class => [
            self::ARRAY_KEY_KEY => DnkPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => DnkPaymentMethod::DEFAULT_NAME
        ],
        EbtPaymentMethod::class => [
            self::ARRAY_KEY_KEY => EbtPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => EbtPaymentMethod::DEFAULT_NAME
        ],
        EpyPaymentMethod::class => [
            self::ARRAY_KEY_KEY => EpyPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => EpyPaymentMethod::DEFAULT_NAME
        ],
        GcbPaymentMethod::class => [
            self::ARRAY_KEY_KEY => GcbPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => GcbPaymentMethod::DEFAULT_NAME
        ],
        GirPaymentMethod::class => [
            self::ARRAY_KEY_KEY => GirPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => GirPaymentMethod::DEFAULT_NAME
        ],
        IdlPaymentMethod::class => [
            self::ARRAY_KEY_KEY => IdlPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => IdlPaymentMethod::DEFAULT_NAME
        ],
        MaePaymentMethod::class => [
            self::ARRAY_KEY_KEY => MaePaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => MaePaymentMethod::DEFAULT_NAME
        ],
        MscPaymentMethod::class => [
            self::ARRAY_KEY_KEY => MscPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => MscPaymentMethod::DEFAULT_NAME
        ],
        NpyPaymentMethod::class => [
            self::ARRAY_KEY_KEY => NpyPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => NpyPaymentMethod::DEFAULT_NAME
        ],
        ObtPaymentMethod::class => [
            self::ARRAY_KEY_KEY => ObtPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => ObtPaymentMethod::DEFAULT_NAME
        ],
        PliPaymentMethod::class => [
            self::ARRAY_KEY_KEY => PliPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => PliPaymentMethod::DEFAULT_NAME
        ],
        PscPaymentMethod::class => [
            self::ARRAY_KEY_KEY => PscPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => PscPaymentMethod::DEFAULT_NAME
        ],
        PspPaymentMethod::class => [
            self::ARRAY_KEY_KEY => PspPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => PspPaymentMethod::DEFAULT_NAME
        ],
        PwyPaymentMethod::class => [
            self::ARRAY_KEY_KEY => PwyPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => PwyPaymentMethod::DEFAULT_NAME
        ],
        SftPaymentMethod::class => [
            self::ARRAY_KEY_KEY => SftPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => SftPaymentMethod::DEFAULT_NAME
        ],
        VsaPaymentMethod::class => [
            self::ARRAY_KEY_KEY => VsaPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => VsaPaymentMethod::DEFAULT_NAME
        ],
        WltPaymentMethod::class => [
            self::ARRAY_KEY_KEY => WltPaymentMethod::KEY,
            self::ARRAY_KEY_DEFAULT_NAME => WltPaymentMethod::DEFAULT_NAME
        ]
    ];

    /**
     * Returns the available payment methods and their helper strings (config-key, payment-key, default name).
     *
     * @return string[]
     */
    public static function getPaymentMethods(): array
    {
        return array_keys(static::$paymentMethods);
    }

    /**
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getPaymentMethodDefaultName(string $paymentMethod): string
    {
        $prefix = Plugin::NAME . ' - ';
        $name = static::$paymentMethods[$paymentMethod][self::ARRAY_KEY_DEFAULT_NAME] ?? self::NO_DEFAULT_NAME_FOUND;

        return $prefix . $name;
    }

    /**
     * This is also used within the PaymentHelper class, so it must be public.
     *
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getPaymentMethodKey(string $paymentMethod): string
    {
        return static::$paymentMethods[$paymentMethod][self::ARRAY_KEY_KEY] ?? self::NO_KEY_FOUND;
    }
}
