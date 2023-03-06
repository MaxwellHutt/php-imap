<?php
/*
* File: Issue355Test.php
* Category: -
* Author: M.Goldenbaum
* Created: 10.01.23 10:48
* Updated: -
*
* Description:
*  -
*/

namespace Tests\issues;

use Tests\LiveMailboxTestCase;
use Webklex\PHPIMAP\Exceptions\AuthFailedException;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\EventNotFoundException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Webklex\PHPIMAP\Exceptions\ImapBadRequestException;
use Webklex\PHPIMAP\Exceptions\ImapServerErrorException;
use Webklex\PHPIMAP\Exceptions\MaskNotFoundException;
use Webklex\PHPIMAP\Exceptions\ResponseException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;

class Issue383Test extends LiveMailboxTestCase {

    /**
     * Test issue #383 - Does not work when a folder name contains umlauts: Entwürfe
     * @return void
     * @throws AuthFailedException
     * @throws ConnectionFailedException
     * @throws EventNotFoundException
     * @throws FolderFetchingException
     * @throws ImapBadRequestException
     * @throws ImapServerErrorException
     * @throws ResponseException
     * @throws RuntimeException
     * @throws MaskNotFoundException
     */
    public function testIssue(): void {
        if (!getenv("LIVE_MAILBOX") ?? false) {
            $this->markTestSkipped("This test requires a live mailbox. Please set the LIVE_MAILBOX environment variable to run this test.");
        }
        $client = $this->getClient();
        $client->connect();

        $delimiter = $this->getManager()->get("options.delimiter");
        $folder_path = implode($delimiter, ['INBOX', 'Entwürfe+']);

        $folder = $client->getFolder($folder_path);
        $this->deleteFolder($folder);

        $folder = $client->createFolder($folder_path, false);
        $this->assertNotNull($folder);
        $folder = $this->getFolder($folder_path);
        $this->assertNotNull($folder);

        $this->assertEquals('Entwürfe+', $folder->name);
        $this->assertEquals($folder_path, $folder->full_name);

        // Clean up
        if ($this->deleteFolder($folder) === false) {
            $this->fail("Could not delete folder: " . $folder->path);
        }
    }
}