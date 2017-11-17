<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Test\BlobStore;

use PHPUnit\Framework\TestCase;
use SetBased\Abc\Abc;
use SetBased\Abc\C;
use SetBased\Abc\CompanyResolver\UniCompanyResolver;
use SetBased\Abc\Test\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
class BlobBlobStoreTestCase extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB.
   */
  protected function getBlobCount()
  {
    return DataLayer::executeSingleton1('select count(*) from `ABC_BLOB`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB_DATA.
   */
  protected function getBlobDataCount()
  {
    return DataLayer::executeSingleton1('select count(*) from `ABC_BLOB_DATA`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the MySQL server and cleans the BLOB tables.
   */
  protected function setUp()
  {
    Abc::$DL = new DataLayer();
    Abc::$companyResolver = new UniCompanyResolver(C::CMP_ID_SYS);

    DataLayer::connect('localhost', 'test', 'test', 'test');

    DataLayer::executeNone('set foreign_key_checks = 0');
    DataLayer::executeNone('truncate table `ABC_BLOB`');
    DataLayer::executeNone('truncate table `ABC_BLOB_DATA`');
    DataLayer::executeNone('set foreign_key_checks = 1');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Disconnects from the MySQL server.
   */
  protected function tearDown()
  {
    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
