<?php


namespace App\Models\Traits;


trait Scopable
{
    /**
     * Global param to allow to unscope model if required. Avoiding sometimes to go though an infinite scope loop
     *
     * @var bool
     */
    protected static $scopedModels = array();
    /**
     * Defines if by default the scope is set or not. MUST be TRUE under production.
     *
     * @var boolean
     */
    protected static $isScopedByDefault = true;

    /**
     * Unset / set scope check on object instance
     *
     * @param bool $scoped
     * @return Object
     */
    public function setIsScoped($scoped)
    {
        get_class($this)::set_scope($scoped);
        return $this;
    }
    /**
     * Get scope staus on object instance
     *
     * @return bool
     */
    public function getIsScoped()
    {
        return get_class($this)::is_scoped();
    }


    /**
     * Scope model
     *
     * @return void
     */
    public static function scope()
    {
        static::set_scope(true);
    }

    /**
     * Unscope model
     *
     * @return void
     */
    public static function unscope()
    {
        static::set_scope(false);
    }

    /**
     * Unset / set scope on model
     *
     * @return bool
     */
    public static function set_scope($scoped)
    {
        static::$scopedModels[get_called_class()] = $scoped;
    }
    /**
     * Get scode status for model
     *
     * @return bool
     */
    public static function is_scoped()
    {
        if(isset(static::$scopedModels[get_called_class()]))
            return static::$scopedModels[get_called_class()];
        else
            return static::$isScopedByDefault;
    }
}
