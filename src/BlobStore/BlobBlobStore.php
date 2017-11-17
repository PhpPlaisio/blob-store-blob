<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\BlobStore;

use SetBased\Abc\Abc;
use SetBased\Helper\ProgramExecution;

/**
 * Class for storing BLOBs (i.e. files, documents, images, data) as BLOBs in the database.
 */
class BlobBlobStore implements BlobStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Path to the file executable.
   *
   * @var string
   */
  public static $filePath = '/usr/bin/file';

  /**
   * Translation of "wrong" mime types to correct mime types.
   *
   * @var array
   */
  public static $mimeTypeTranslate = ['application/x-pdf' => 'application/pdf'];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function delBlob($blbId)
  {
    Abc::$DL->abcBlobDelBlob(Abc::$companyResolver->getCmpId(), $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getBlob($blbId)
  {
    return Abc::$DL->abcBlobGetBlob(Abc::$companyResolver->getCmpId(), $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getMetadata($blbId)
  {
    return Abc::$DL->abcBlobGetMetadata(Abc::$companyResolver->getCmpId(), $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function putFile($path, $filename, $mimeType = null, $timestamp = null)
  {
    // If required determine the mime type of the file.
    if ($mimeType===null)
    {
      $mimeType = $this->getMimeType($path);
    }

    // Get the BLOB data from file.
    $data = file_get_contents($path);

    // Insert the BLOB (data and metadata) into the database.
    Abc::$DL->abcBlobInsertBlob(Abc::$companyResolver->getCmpId(), $filename, $mimeType, $timestamp, $data);

    return Abc::$DL->abcBlobWorkaround();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function putString($data, $filename, $mimeType, $timestamp = null)
  {
    // If required translate the mime type.
    if (isset(self::$mimeTypeTranslate[$mimeType]))
    {
      $mimeType = self::$mimeTypeTranslate[$mimeType];
    }

    Abc::$DL->abcBlobInsertBlob(Abc::$companyResolver->getCmpId(), $filename, $mimeType, $timestamp, $data);

    return Abc::$DL->abcBlobWorkaround();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function searchByMd5($md5)
  {
    return Abc::$DL->abcBlobGetMetadataByMd5(Abc::$companyResolver->getCmpId(), $md5);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the mime type of a file.
   *
   * @param string $path The path to the file.
   *
   * @return string
   *
   * @api
   * @since 1.0.0
   */
  protected function getMimeType($path)
  {
    list($output) = ProgramExecution::exec1([self::$filePath, '-ib', $path], [0], true);

    $mimeType = $output[0];

    // If required translate the mime type.
    if (isset(self::$mimeTypeTranslate[$mimeType]))
    {
      $mimeType = self::$mimeTypeTranslate[$mimeType];
    }

    return $mimeType;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
