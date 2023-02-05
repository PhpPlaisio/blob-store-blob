<?php
declare(strict_types=1);

namespace Plaisio\BlobStore\Test;

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
  public function baseStoreAndRetrieve(string $method): void
  {
    if ($method=='putFile')
    {
      $blbId = $this->kernel->blob->putFile(__FILE__, basename(__FILE__));
    }
    else
    {
      $blbId = $this->kernel->blob->putString(file_get_contents(__FILE__), basename(__FILE__));
    }

    // Search by MD5 hash must give 1 row.
    self::assertCount(1, $this->kernel->blob->searchByMd5(md5_file(__FILE__)));

    // Test all fields of getBlob.
    $blob = $this->kernel->blob->getBlob($blbId);
    self::assertEquals(1, $blob['blb_id']);
    self::assertEquals(md5_file(__FILE__), strtolower($blob['blb_md5']));
    self::assertEquals(filesize(__FILE__), $blob['blb_size']);
    self::assertEquals(basename(__FILE__), $blob['blb_filename']);
    self::assertNotEmpty($blob['blb_mime_type']);
    self::assertNotEmpty($blob['blb_timestamp']);
    self::assertSame(file_get_contents(__FILE__), $blob['blb_data']);

    // Test all fields of getMetaData.
    $meta_data = $this->kernel->blob->getMetadata($blbId);
    self::assertEquals(1, $meta_data['blb_id']);
    self::assertEquals(md5_file(__FILE__), strtolower($meta_data['blb_md5']));
    self::assertEquals(filesize(__FILE__), $meta_data['blb_size']);
    self::assertEquals(basename(__FILE__), $meta_data['blb_filename']);
    self::assertNotEmpty($meta_data['blb_mime_type']);
    self::assertNotEmpty($meta_data['blb_timestamp']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing files with MD5 collision.
   */
  public function testCollision(): void
  {
    // The files below are taken from http://natmchugh.blogspot.be/2014/10/how-i-made-two-php-files-with-same-md5.html.
    $file1 = __DIR__.'/a.php';
    $file2 = __DIR__.'/b.php';

    $blbId1 = $this->kernel->blob->putFile($file1, basename($file1));
    $blbId2 = $this->kernel->blob->putFile($file2, basename($file2));

    // We expect 2 rows in ABC_BLOB and ABC_BLOB_DATA.
    self::assertEquals(2, $this->getBlobCount(), 'ABC_BLOB');
    self::assertEquals(2, $this->getBlobDataCount(), 'ABC_BLOB_DATA');

    $blob1 = $this->kernel->blob->getBlob($blbId1);
    $blob2 = $this->kernel->blob->getBlob($blbId2);

    // Hashes must be equal but data not.
    self::assertSame($blob1['blb_md5'], $blob2['blb_md5']);
    self::assertNotSame($blob1['blb_data'], $blob2['blb_data']);

    // Test data is retrieved correctly.
    self::assertSame(file_get_contents($file1), $blob1['blb_data']);
    self::assertSame(file_get_contents($file2), $blob2['blb_data']);

    // Search by MD5 hash must give 2 rows.
    self::assertCount(2, $this->kernel->blob->searchByMd5($blob1['blb_md5']));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing twice the same for different companies will not use the same BLOB.
   */
  public function testCompanySeparation(): void
  {
    $this->kernel->blob->putFile(__FILE__, basename(__FILE__));

    $this->kernel = new TestKernelPlaisio();

    $this->kernel->blob->putFile(__FILE__, basename(__FILE__));

    self::assertEquals(2, $this->getBlobCount(), 'ABC_BLOB');
    self::assertEquals(2, $this->getBlobDataCount(), 'ABC_BLOB_DATA');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing and deleting.
   */
  public function testDelBlob(): void
  {
    $blbId = $this->kernel->blob->putFile(__FILE__, basename(__FILE__));
    self::assertEquals(1, $this->getBlobCount(), 'ABC_BLOB');

    $this->kernel->blob->delBlob($blbId);
    self::assertEquals(0, $this->getBlobCount(), 'ABC_BLOB');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test mime type translation for method putString.
   */
  public function testMimeTypeTranslationPutString(): void
  {
    $blbId = $this->kernel->blob->putString('%PDF-1.1', 'test.pdf');

    $blob = $this->kernel->blob->getMetadata($blbId);

    self::assertStringStartsWith('application/pdf', $blob['blb_mime_type']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing twice the same file must reuse the same BLOB.
   */
  public function testReUsage(): void
  {
    $this->kernel->blob->putFile(__FILE__, basename(__FILE__));
    $this->kernel->blob->putFile(__FILE__, basename(__FILE__));

    self::assertEquals(2, $this->getBlobCount(), 'ABC_BLOB');
    self::assertEquals(1, $this->getBlobDataCount(), 'ABC_BLOB_DATA');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing and retrieving.
   */
  public function testStoreAndRetrieveFile(): void
  {
    $this->baseStoreAndRetrieve('putFile');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test storing and retrieving.
   */
  public function testStoreAndRetrieveString(): void
  {
    $this->baseStoreAndRetrieve('putString');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
