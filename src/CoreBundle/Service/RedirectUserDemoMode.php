<?php

namespace CoreBundle\Service;

class RedirectUserDemoMode {

    private $appInDemoMode;
    private $demoModeDisabledActions;
    private $routeToRedirectUserTo;

    public function __construct($appInDemoMode, $demoModeDisabledActions, $routeToRedirectUserTo)
    {

        $this->appInDemoMode = $appInDemoMode;
        $this->demoModeDisabledActions = $demoModeDisabledActions;
        $this->routeToRedirectUserTo = $routeToRedirectUserTo;
    }

    public function hasUserToBeRedirected($currentRoute, $requestMethod){

        if($this->isAppInDemoMode()) {
            // Request method can be an array like (POST,GET) or just a string like (POST), see parameters.yml
            if(array_key_exists($currentRoute, $this->getDemoModedisabledActions())) {
                if(is_array($this->getDemoModedisabledActions()[$currentRoute])) {
                    if (in_array($requestMethod, $this->getDemoModedisabledActions()[$currentRoute] )) {
                        return $this->routeToRedirectUserTo;
                    }
                } else if ($requestMethod == $this->getDemoModedisabledActions()[$currentRoute]
                    || $this->getDemoModedisabledActions()[$currentRoute] == "ANY") {
                        return $this->routeToRedirectUserTo;
                }
            }
        }

        return false;
    }

    public function getRouteToRedirectUserTo(){
        return $this->routeToRedirectUserTo;
    }

    private function isAppInDemoMode()
    {
        return $this->appInDemoMode;
    }

    private function getDemoModedisabledActions(){
        return $this->demoModeDisabledActions;
    }


}