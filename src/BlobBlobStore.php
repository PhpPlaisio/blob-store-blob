<?php
declare(strict_types=1);

namespace Plaisio\BlobStore;

use Plaisio\PlaisioObject;
use SetBased\Helper\ProgramExecution;

/**
 * Class for storing BLOBs (i.e. files, documents, images, data) as BLOBs in the database.
 */
class BlobBlobStore extends PlaisioObject implements BlobStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Path to the file executable.
   *
   * @var string
   */
  public static string $filePath = '/usr/bin/file';

  /**
   * Directory for storing temporary files. If null sys_get_temp_dir() will be used.
   *
   * @var string|null
   */
  public static ?string $tmpDir = null;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function delBlob(int $blbId): void
  {
    $this->nub->DL->abcBlobStoreBlobDelBlob($this->nub->company->cmpId, $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getBlob(int $blbId): array
  {
    return $this->nub->DL->abcBlobStoreBlobGetBlob($this->nub->company->cmpId, $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getMetadata(int $blbId): array
  {
    return $this->nub->DL->abcBlobStoreBlobGetMetadata($this->nub->company->cmpId, $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function mimeTypePath(string $path): string
  {
    [$output] = ProgramExecution::exec1([self::$filePath, '-ib', $path], [0], true);

    return $output[0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function mimeTypeString(string $data): string
  {
    $path   = tempnam(static::$tmpDir ?? sys_get_temp_dir(), 'mime-');
    $handle = fopen($path, 'wb');
    fwrite($handle, $data);
    fclose($handle);

    try
    {
      $mimeType = $this->mimeTypePath($path);
    }
    finally
    {
      unlink($path);
    }

    return $mimeType;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function putFile(string $path, string $filename, ?string $mimeType = null, ?string $timestamp = null): int
  {
    if ($mimeType===null)
    {
      $mimeType = $this->mimeTypePath($path);
    }

    $data = file_get_contents($path);

    $this->nub->DL->abcBlobStoreBlobInsertBlob($this->nub->company->cmpId, $filename, $mimeType, $timestamp, $data);

    return $this->nub->DL->abcBlobStoreBlobWorkaround();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function putString(string $data, string $filename, ?string $mimeType = null, ?string $timestamp = null): int
  {
    if ($mimeType===null)
    {
      $mimeType = $this->mimeTypeString($data);
    }

    $this->nub->DL->abcBlobStoreBlobInsertBlob($this->nub->company->cmpId, $filename, $mimeType, $timestamp, $data);

    return $this->nub->DL->abcBlobStoreBlobWorkaround();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function searchByMd5(string $md5): array
  {
    return $this->nub->DL->abcBlobStoreBlobGetMetadataByMd5($this->nub->company->cmpId, $md5);
  }
}

//----------------------------------------------------------------------------------------------------------------------
