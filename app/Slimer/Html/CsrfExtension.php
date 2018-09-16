<?php
/**
 * Author: Shawn Chen
 * Desc: The Slim csrf extension
 */
namespace Slimer\Html;

/**
 * slim/csrf twig extension.
 *
 * @author slim
 *
 * @see https://github.com/slimphp/Slim-Csrf#accessing-the-token-pair-in-templates-twig-etc
 */
class CsrfExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    /**
     * @var \Slim\Csrf\Guard
     */
    protected $csrf;
    
    /**
     * @var \Slim\Csrf\Guard
     */
    public function __construct(\Slim\Csrf\Guard $csrf)
    {
        $this->csrf = $csrf;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        // CSRF token name and value
        $csrfNameKey = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName = $this->csrf->getTokenName();
        $csrfValue = $this->csrf->getTokenValue();
        
        return [
            'csrf' => [
                'keys' => [
                    'name' => $csrfNameKey,
                    'value' => $csrfValueKey,
                ],
                'name' => $csrfName,
                'value' => $csrfValue,
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'slim/csrf';
    }
}
