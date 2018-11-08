<?php
/**
 * Author: Shawn Chen
 * Desc: The AdminLTE sideBar extension for the twig
 */
namespace Slimer\Html;

use Psr\Http\Message\ServerRequestInterface;
use Pimple\Container;

/**
 * slim/sidebar extension 
 *
 * @author slim
 *
 * @see 
 */
class SideBarExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    /**
     * @var container
     */
    
    protected $container;
    
    /**
     * @var \Slim\Csrf\Guard
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'sidebar_menu',
                [$this, 'SidebarMenuFunction'],
                ['is_safe' => ['html'], 'needs_environment' => true]
                ),
            new \Twig_SimpleFunction(
                'sidebar_toggle_button',
                [$this, 'ToggleButtonFunction'],
                ['is_safe' => ['html'], 'needs_environment' => true]
                ),
            new \Twig_SimpleFunction(
                'sidebar_collapse',
                [$this, 'SidebarCollapseFunction'],
                ['is_safe' => ['html'], 'needs_environment' => false]
                ),
            new \Twig_SimpleFunction(
                 'isSubMenuActive',
                [$this, 'isSubMenuActive'],
                ['is_safe' => ['html'], 'needs_environment' => true]
                ),
            new \Twig_SimpleFunction(
                'getVersion',
                [$this, 'getVersion'],
                ['is_safe' => ['html'], 'needs_environment' => false]
                ),
            new \Twig_SimpleFunction(
                'getCommitId',
                [$this, 'getCommitId'],
                ['is_safe' => ['html'], 'needs_environment' => false]
                )
            
        ];
    }
    
    function getFilters()
    {
        return [
            new \Twig_SimpleFilter('render_empty', [$this, 'RenderEmpty'],['is_safe' => ['html'], 'needs_environment' => false]),
        ];
    }
    
    public function RenderEmpty($value){
        if (isset($value) && ($value === '' || $value === ' ')){
            return "&nbsp";
        }else{
            return $value;
        }
    }
    
    
    
    public function SidebarMenuFunction(\Twig_Environment $environment, ServerRequestInterface $request)
    {
        
        /** @var SidebarMenuEvent $menuEvent */
        if ($this->container['session']->get('user') == null) return '';
        return $environment->render('adminlte/sidebar/menu.html.twig', ['menu' =>$this->container['config']('menu'),'request'=>$request]);
    }
    
    public function SidebarCollapseFunction($session)
    {
        if ($session->get('sbs_adminlte_sidebar_collapse') === 'true') {
            return 'sidebar-collapse';
        }
        return '';
    }
    
    public function ToggleButtonFunction(\Twig_Environment $environment)
    {
        /** @var RoutingExtension $routing */
        $template = '<a href="#" class="sidebar-toggle" data-toggle="push-menu"><span class="sr-only">Toggle navigation</span></a>';
        
        try {
            $url = $this->container['router']->pathFor('sbs_adminlte_sidebar_collapse');
            return $environment
            ->createTemplate($template . '<script>
                    $(function () {
                        $(document).on("click", ".sidebar-toggle", function () {
                            event.preventDefault();
                            $.post("{{ url }}", {collapse: $("body").hasClass("sidebar-collapse")} );
                        });
                    });</script>')->render(['url' => $url]);
        } catch (\Exception $e) {
            return $template;
        }
    }
    
    
    public function isSubMenuActive(\Twig_Environment $environment, $children, $current_route)
    {
        if ($children != null and sizeof($children)>0){
            foreach($children as $m){
                if (isset($m['route']) && strpos($m['route'],$current_route)){
                    return true;
                }
                if (isset($m['routeName']) && strpos($m['routeName'],$current_route)){
                    return true;
                }
            }

        }
        return false;
    }
    
    public function getVersion()
    {
        $key=$this->container['config']('suit.version_key') ? $this->container['config']('suit.version_key') : "VERSION";
        return getenv($key) ?  getenv($key) : null;
    }
    
    public function getCommitId()
    {
        $key=$this->container['config']('suit.commitid_key') ? $this->container['config']('suit.commitid_key') : "COMMITID";
        return getenv($key) ?  getenv($key) : null;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Slimer/Html/SideBarExtension';
    }
}
