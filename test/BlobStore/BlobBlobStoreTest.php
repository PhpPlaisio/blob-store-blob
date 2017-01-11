<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Test\BlobStore;

use SetBased\Abc\BlobStore\BlobBlobStore;
use SetBased\Abc\C;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Test cases for BlobBlobStore
 */
class BlobBlobStoreTest extends BlobBlobStoreTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing and retrieving.
   *
   * @param string $method Either putFile or putString.
   */
  public function baseStoreAndRetrieve($method)
  {
    $store = new BlobBlobStore();

    if ($method=='putFile')
    {
      $blb_id = $store->putFile(C::CMP_ID_SYS, __FILE__, basename(__FILE__));
    }
    else
    {
      $blb_id = $store->putString(C::CMP_ID_SYS, file_get_contents(__FILE__), basename(__FILE__), 'application/x-php');
    }

    // Search by MD5 hash must give 1 row.
    $this->assertCount(1, $store->searchByMd5(C::CMP_ID_SYS, md5_file(__FILE__)));

    // Test all fields of getBlob.
    $blob = $store->getBlob(C::CMP_ID_SYS, $blb_id);
    $this->assertEquals(1, $blob['blb_id']);
    $this->assertEquals(md5_file(__FILE__), strtolower($blob['blb_md5']));
    $this->assertEquals(filesize(__FILE__), $blob['blb_size']);
    $this->assertEquals(basename(__FILE__), $blob['blb_filename']);
    $this->assertNotEmpty($blob['blb_mime_type']);
    $this->assertNotEmpty($blob['blb_timestamp']);
    $this->assertSame(file_get_contents(__FILE__), $blob['blb_data']);

    // Test all fields of getMetaData.
    $meta_data = $store->getMetadata(C::CMP_ID_SYS, $blb_id);
    $this->assertEquals(1, $meta_data['blb_id']);
    $this->assertEquals(md5_file(__FILE__), strtolower($meta_data['blb_md5']));
    $this->assertEquals(filesize(__FILE__), $meta_data['blb_size']);
    $this->assertEquals(basename(__FILE__), $meta_data['blb_filename']);
    $this->assertNotEmpty($meta_data['blb_mime_type']);
    $this->assertNotEmpty($meta_data['blb_timestamp']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing files with MD5 collision.
   */
  public function testCollision()
  {
    $store = new BlobBlobStore();

    // The files below are taken from http://natmchugh.blogspot.be/2014/10/how-i-made-two-php-files-with-same-md5.html.
    $file1 = __DIR__.'/a.php';
    $file2 = __DIR__.'/b.php';

    $blb_id1 = $store->putFile(C::CMP_ID_SYS, $file1, basename($file1));
    $blb_id2 = $store->putFile(C::CMP_ID_SYS, $file2, basename($file2));

    // We expect 2 rows in ABC_BLOB and ABC_BLOB_DATA.
    $this->assertEquals($this->getBlobCount(), 2, 'ABC_BLOB');
    $this->assertEquals($this->getBlobDataCount(), 2, 'ABC_BLOB_DATA');

    $blob1 = $store->getBlob(C::CMP_ID_SYS, $blb_id1);
    $blob2 = $store->getBlob(C::CMP_ID_SYS, $blb_id2);

    // Hashes must be equal but data not.
    $this->assertSame($blob1['blb_md5'], $blob2['blb_md5']);
    $this->assertNotSame($blob1['blb_data'], $blob2['blb_data']);

    // Test data is retrieved correctly.
    $this->assertSame(file_get_contents($file1), $blob1['blb_data']);
    $this->assertSame(file_get_contents($file2), $blob2['blb_data']);

    // Search by MD5 hash must give 2 rows.
    $this->assertCount(2, $store->searchByMd5(C::CMP_ID_SYS, $blob1['blb_md5']));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing twice the same for different companies will not use the same BLOB.
   */
  public function testCompanySeparation()
  {
    $store = new BlobBlobStore();

    $store->putFile(C::CMP_ID_SYS, __FILE__, basename(__FILE__));
    $store->putFile(C::CMP_ID_ABC, __FILE__, basename(__FILE__));

    $this->assertEquals($this->getBlobCount(), 2, 'ABC_BLOB');
    $this->assertEquals($this->getBlobDataCount(), 2, 'ABC_BLOB_DATA');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing and deleting.
   */
  public function testDelBlob()
  {
    $store = new BlobBlobStore();

    $blb_id = $store->putFile(C::CMP_ID_SYS, __FILE__, basename(__FILE__));
    $this->assertEquals($this->getBlobCount(), 1, 'ABC_BLOB');

    $store->delBlob(C::CMP_ID_SYS, $blb_id);
    $this->assertEquals($this->getBlobCount(), 0, 'ABC_BLOB');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test mime type translation for method putString.
   */
  public function testMimeTypeTranslationPutString()
  {
    $store = new BlobBlobStore();

    $blb_id = $store->putString(C::CMP_ID_SYS, '%PDF-1.1', 'test.pdf', 'application/x-pdf');

    $blob = $store->getMetadata(C::CMP_ID_SYS, $blb_id);

    $this->assertSame('application/pdf', $blob['blb_mime_type']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing twice the same file must reuse the same BLOB.
   */
  public function testReUsage()
  {
    $store = new BlobBlobStore();

    $store->putFile(C::CMP_ID_SYS, __FILE__, basename(__FILE__));
    $store->putFile(C::CMP_ID_SYS, __FILE__, basename(__FILE__));

    $this->assertEquals($this->getBlobCount(), 2, 'ABC_BLOB');
    $this->assertEquals($this->getBlobDataCount(), 1, 'ABC_BLOB_DATA');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing and retrieving.
   */
  public function testStoreAndRetrieveFile()
  {
    $this->baseStoreAndRetrieve('putFile');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing and retrieving.
   */
  public function testStoreAndRetrieveString()
  {
    $this->baseStoreAndRetrieve('putString');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
