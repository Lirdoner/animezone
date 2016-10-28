<?php


namespace Sequence\Cache\Driver;


use Sequence\Cache\DriverInterface;

class PlainFile implements DriverInterface
{
    /** @var  array */
    protected $options;

    /**
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        $this->options = array_merge(array(
            'path' => null,
            'extension' => '.cache'
        ), $options);

        if(empty($this->options['path']))
        {
            throw new \InvalidArgumentException('Missing "path" argument.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $filePath = false)
    {
        $file = $this->options['path'].$name.$this->options['extension'];
        $fileInfo = new \SplFileInfo($file);

        if(!$fileInfo->isFile() || !$fileInfo->isReadable())
        {
            return null;
        }

        if($fileInfo->getMTime() < time())
        {
            $this->delete($name);

            return null;
        }

        if($filePath)
        {
            return $fileInfo->getPathname();
        }

        return file_get_contents($file);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value, $ttl = 0)
    {
        $this->checkDir($name);

        $file = $this->options['path'].$name.$this->options['extension'];

        if(!file_put_contents($file, $value))
        {
            throw new \RuntimeException(sprintf('Failed to create file: "%s".', $file));
        }

        if(!$ttl)
        {
            $ttl = 31536000;
        }

        if(!touch($file, time() + $ttl))
        {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return file_exists($this->options['path'].$name.$this->options['extension']);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function delete($name)
    {
        $file = $this->options['path'].$name.$this->options['extension'];

        if($this->exists($name))
        {
            if(!is_writable($file))
            {
                throw new \RuntimeException(sprintf('File "%s" is not readable.', $file));
            }

            return unlink($file);
        } else
        {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup($group)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->options['path'].$group), true);

        $items = 0;

        foreach($iterator as $file)
        {
            if($file->isFile() && $file->isWritable())
            {
                $items += (int)unlink($file->getPathname());
            }
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll()
    {
        return $this->deleteGroup(null);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'plain_file';
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->options['extension'];
    }

    /**
     * @param string $dir
     *
     * @throws \RuntimeException
     */
    protected function checkDir($dir)
    {
        $dir = dirname($this->options['path'].$dir);

        if(is_dir($dir))
        {
            if(!is_writable($dir))
            {
                chmod($dir, 0777);
            }
        } else
        {
            if(!mkdir($dir, 0777, true))
            {
                throw new \RuntimeException(sprintf('Failed to create folder: "%s".', $dir));
            }
        }
    }
} 