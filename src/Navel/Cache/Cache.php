<?php

namespace Navel\Cache;

/**
 * Class Cache
 *
 * <ul>
 * <li>Used to store parts of application in files or db</li>
 * <li>Current version only support file storage: SQLite and MySQL db storage coming soon.</li>
 * </ul>
 *
 * @package Navel\Cache
 */
class Cache
{
    /**
     * [protected path]
     * @var string
     */
    protected $path;
    /**
     * [protected lifetime]
     * @var int Cache lifetime in minutes
     */
    protected $lifetime;
    /**
     * [protected buffer]
     * @var string|bool
     */
    protected $buffer;

    /**
     * Function end
     *
     * End started cache buffer
     *
     * @return void
     */
    public function end()
    {
        if(!is_null($this->buffer))
        {
            $content = ob_get_clean();
            $this->write($this->buffer, $content);
            $this->buffer = null;
            echo $content;
        }
    }

    /**
     * Function start
     *
     * <ul>
     * <li>Used to store portion of a page.</li>
     * <li>Can be used with alternative php syntax.</li>
     * <li>Must be followed by end function. Can't be nested.</li>
     * </ul>
     *
     *
     * <code>
     * [Exemple: using regular syntax]
     *
     * // Create one hour cache lifetime in folder /path/to/cache
     * $cache = new Navel\Cache\Cache('/path/to/cache', 60);
     * if($cache->start('test'))
     * {
     *     echo $variable.'</br>test';
     *     ...
     * }
     * $cache->end();
     *
     * </code>
     *
     * <code>
     * [Exemple: using alternative syntax]
     *
     * <?php
     * ...
     * // Create one hour cache lifetime in folder /path/to/cache
     * $cache = new Navel\Cache\Cache('/path/to/cache', 60);
     * if($cache->start('test')): ?>
     * <test><?php echo $variable.'</br>test'; ?></test>
     * ...
     * <?php endif; ?>
     * <?php $cache->end(); ?>
     *
     * </code>
     *
     * @param  string $name Cache name
     * @return bool         If we can use cache or not
     */
    public function start(string $name):bool
    {
        if($content = $this->read($name))
        {
            $this->buffer = null;
            echo $content;
            return false;
        }
        ob_start();
        $this->buffer = $name;
        return true;
    }

    /**
     * Function incl
     *
     * Used to store complete pages or external scripts
     * which can do long treatments.
     *
     * <code>
     * [Exemple]
     *
     * // Create one hour cache lifetime in folder /path/to/cache
     * $cache = new Navel\Cache\Cache('/path/to/cache', 60);
     *
     * // include file to cache
     * $cache->incl('script.php');
     *
     * </code>
     *
     * @param  string      $file The file to store
     * @param  string|null $name The cache name (optional)
     * @return bool
     */
    public function incl(string $file, string $name=null):bool
    {
        if(is_null($name))
        {
            $name = basename($file);
        }
        if($content = $this->read($name))
        {
            echo $content;
            return true;
        }
        ob_start();
        require $file;
        $content = ob_get_clean();
        $this->write($name, $content);
        echo $content;
        return true;
    }

    /**
     * Function write
     *
     * Write cache in file.
     *
     * <code>
     * [Exemple]
     *
     * // Create one hour cache lifetime in folder /path/to/cache
     * $cache = new Navel\Cache\Cache('/path/to/cache', 60);
     *
     * // try to read cache
     * if(!$variable = $cache->read('variable')) {
     *
     *     // if no cache:
     *     sleep(2); // long treatment ;-)
     *
     *     // assign variable
     *     $variable = 'Lorem ipsum';
     *
     *     // then store it
     *     $cache->write('variable', $variable);
     * }
     *
     * // Display variable
     * echo $variable;
     * </code>
     *
     * @param  string $name    Cache name
     * @param  string $content Content to store in cache
     * @return bool
     */
    public function write(string $name, string $content):bool
    {
        return file_put_contents($this->path.'/'.$name, $content);
    }

    /**
     * Function read
     *
     * Read a stored cache.
     *
     * <code>
     * [Exemple]
     *
     * // Create one hour cache lifetime in folder /path/to/cache
     * $cache = new Navel\Cache\Cache('/path/to/cache', 60);
     *
     * // try to read cache
     * if(!$variable = $cache->read('variable')) {
     *
     *     // if no cache:
     *     sleep(2); // long treatment ;-)
     *
     *     // assign variable
     *     $variable = 'Lorem ipsum';
     *
     *     // then store it
     *     $cache->write('variable', $variable);
     * }
     *
     * // Display variable
     * echo $variable;
     * </code>
     *
     * @param  string $name Cache file name
     * @return bool|string  Returns false if file does not exist or when cache is expired
     */
    public function read(string $name)
    {
        $file = $this->path.'/'.$name;
        if(!file_exists($file))
        {
            return false;
        }
        if((time() - filemtime($file)) / 60 >= $this->lifetime)
        {
            return false;
        }
        return file_get_contents($file);
    }

    /**
     * Function clear
     *
     * Delete the cache. If name is ommited, all the cache is ereased.
     *
     * <code>
     * [Exemple: Clear myCacheName]
     *
     * // Create one hour cache lifetime in folder /path/to/cache
     * $cache = new Navel\Cache\Cache('/path/to/cache', 60);
     *
     * // Clear cache
     * $cache->clear('myCacheName');
     * </code>
     *
     * <code>
     * [Exemple: Clear all caches]
     *
     * // Create one hour cache lifetime in folder /path/to/cache
     * $cache = new phangu\cache\Cache('/path/to/cache', 60);
     *
     * // Clear cache
     * $cache->clear();
     * </code>
     *
     * @param  string|null  $name  Cache name to clear, clear all if ommited (optional)
     * @return void
     */
    public function clear(string $name=null)
    {
        if(is_null($name))
        {
            $files = glob($this->path.'/*');
            foreach ($files as $file) {
                unlink($file);
            }
        } else {
            if(file_exists($this->path.'/'.$name))
            {
                unlink($this->path.'/'.$name);
            }
        }

    }

    /**
    * Magic Method __get
    *
    * @internal getter for read only members
    *
    * @param  string $name [description]
    * @return mixed       [description]
    */
    public function __get(string $name)
    {
        return $this->$name;
    }

    /**
     * Constructor
     *
     * @param string $path     Path folder where to store the cache
     * @param int    $lifetime Cache lifetime in minutes
     */
    public function __construct(string $path, int $lifetime)
    {
        $this->path = $path;
        $this->lifetime = $lifetime;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->lifetime);
        unset($this->path);
    }
}
