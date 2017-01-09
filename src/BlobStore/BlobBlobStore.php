<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\BlobStore;

use SetBased\Abc\Abc;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for storing BLOBs (i.e. files, documents, images, data) as BLOBs in the database.
 */
class BlobBlobStore implements BlobStore
{
  //--------------------------------------------------------------------------------------------------------------------
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
  public function delBlob($cmpId, $blbId)
  {
    Abc::$DL->abcBlobDelBlob($cmpId, $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getBlob($cmpId, $blbId)
  {
    return Abc::$DL->abcBlobGetBlob($cmpId, $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getMetadata($cmpId, $blbId)
  {
    return Abc::$DL->abcBlobGetMetaData($cmpId, $blbId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function putFile($cmpId, $path, $filename, $mimeType = null)
  {
    // If required determine the mime type of the file.
    if ($mimeType===null)
    {
      $mimeType = $this->getMimeType($path);
    }

    // Get the BLOB data from file.
    $data = file_get_contents($path);

    // Insert the BLOB (data and info) into the database.
    Abc::$DL->abcBlobInsertBlob($cmpId, $filename, $mimeType, null, $data);

    return Abc::$DL->abcBlobWorkaround();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function putString($cmpId, $filename, $mimeType, $data)
  {
    Abc::$DL->abcBlobInsertBlob($cmpId, $filename, $mimeType, null, $data);

    return Abc::$DL->abcBlobWorkaround();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function searchByMd5($cmpId, $md5)
  {
    return Abc::$DL->abcBlobGetMetadataByMd5($cmpId, hex2bin($md5));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the mime type of a file.
   *
   * @param string $path The path to the file.
   *
   * @return string
   */
  protected function getMimeType($path)
  {
    $mimeType = mime_content_type($path);

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
