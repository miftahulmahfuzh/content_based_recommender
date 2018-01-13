<?php

class Uploader_File
{
  const UPLOAD_DIR_NAME = 'upload';
    
  protected $maxSize           = 1; // MB
  protected $allowedExtensions = array();

  protected $uploadDirPath = "";
  
  public function __construct($dir = null)
  {
    $this->setDir($dir);
  }

  public function setDir($dir, $append = true)
  {
    if (empty($dir)) {
      $dir = PROJECT_ROOT . '/' . self::UPLOAD_DIR_NAME;
    } else {
      $dir = PROJECT_ROOT . '/' . ltrim($dir, '/');
    }
    
    if (file_exists($dir) && is_file($dir)) {
      throw new Exception(__METHOD__ . "() '{$dir}' is a file.");
    }

    if (!file_exists($dir)) {
      if ($append) {
        if (!mkdir($dir, 0777, true)) {
          throw new Exception(__METHOD__ . "() Failed to create directory '{$dir}'.");
        }
      } else {
        throw new Exception(__METHOD__ . "() Directory not found. '{$dir}'");
      }
    }

    $this->uploadDirPath = $dir;
  }

  public function setAllowedExtensions($exts)
  {
    if (!is_array($exts)) {
      $exts = array($exts);
    }

    $this->allowedExtensions = array_map('strtolower', $exts);
  }

  public function getAllowedExtensions()
  {
    return $this->allowedExtensions;
  }

  public function setMaxSize($maxSize) 
  {
    $this->maxSize = $maxSize;
  }

  public function getMaxSize()
  {
    return $this->maxSize;
  }

  public function isExtensionValid($fileName)
  {
    if (empty($this->allowedExtensions)) {
      return true;
    } else {
      return (in_array($this->getExtension($fileName), $this->allowedExtensions));
    }
  }

  public function isSizeValid($size)
  {
    return ($size <= ($this->maxSize * 1048576));
  }

  public function generateFileName($ext = null)
  {
    $name = generate_random_string();

    if (!empty($ext)) {
      $name .= '.' . $ext;
    }
    
    if (file_exists($this->uploadDirPath . '/' . $name)) {
      $name = $this->generateFileName($ext);
    }
    
    return $name;
  }
  
  public function getExtension($fileName, $toLowerCase = true)
  {
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);

    return ($toLowerCase) ? strtolower($ext) : $ext;
  }

  public function upload($data, $fileName)
  {
    $filePath = $this->uploadDirPath . '/' . $fileName;
    if (!file_put_contents($filePath, $data)) {
      throw new Exception(__METHOD__ . "() Failed to upload a file. '{$filePath}'"); 
    }
    
    chmod($filePath, 0777);

    return true;
  }

  public function delete($fileName, $move = false)
  {
    if ($move) {
      $srcPath = $this->uploadDirPath . '/' . $fileName;
      $dstDir  = dirname($this->uploadDirPath) . '/' . basename($this->uploadDirPath) . '_deleted';
      $dstPath = $dstDir . '/' . $fileName;
      
      if (!is_dir($dstDir)) {
        if (!mkdir($dstDir, 0777, true)) {
          throw new Exception(__METHOD__ . "() Failed to create directory '{$dstDir}'.");
        }
      }
      
      if (!rename($srcPath, $dstPath)) {
        throw new Exception(__METHOD__ . "() Failed to move a file. '{$srcPath}' -> '{$dstPath}'"); 
      }
    } else {
      $filePath = $this->uploadDirPath . '/' . $fileName;
      
      if (file_exists($filePath)) {
        if (!unlink($filePath)) {
          throw new Exception(__METHOD__ . "() Failed to delete a file. '{$filePath}'"); 
        } 
      }
    }

    return true;
  }

  public function validate($file)
  {
    $errors = array();
    
    if (isset($file['name']) && !$this->isExtensionValid($file['name'])) {
      $exts     = implode(', ', $this->allowedExtensions);
      $errors[] = "Invalid image extension. {$validExt} only.";
    }
    
    if (isset($file['size']) && !$this->isSizeValid($file['size'])) {
      $errors[] = "Image size must be {$this->maxSize}MB or less.";
    }
    
    return $errors;
  }
}
