<?php

namespace Tests\Unit;

use BicBucStriim\BicBucStriim;
use RedBeanPHP\R;
use BicBucStriim\DataConstants;

/**
 * RedBean caches some database information between tests, despite nuking and closing. To avoid this we run the tests
 * in separate processes.
 *
 * NOTE: Debugging the test is not possible with these annotations! Switch them off, if debugging.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TestOfBicBucStriim extends \PHPUnit\Framework\TestCase {

    const SCHEMA = __DIR__ . '/../../data/schema.sql';
    const DB2 = __DIR__ . '/../fixtures/data2.db';
    const AUTHOR1 = __DIR__ . '/../fixtures/author1.jpg';
    const PUBLICS = __DIR__ . '/../../public';

    const WORK = __DIR__ . '/../twork';
    const PUBLICP = __DIR__ . '/../twork/public';
    const THUMB = __DIR__ .'/../twork/public/thumbnails';
    const THUMBA = __DIR__ .'/../twork/public/thumbnails/authors';
    const THUMBT = __DIR__ .'/../twork/public/thumbnails/titles';
    const DATA = __DIR__ . '/../twork/data';
    const TESTSCHEMA = __DIR__ . '/../twork/data/schema.sql';
    const DATADB = __DIR__ . '/../twork/data/data.db';

    var $bbs;

    function setUp() {
        if (file_exists(self::WORK))
            system("rm -rf ".self::WORK);
        mkdir(self::DATA, 0777, true);
        copy(self::DB2, self::DATADB);
        chmod(self::DATADB, 0777);
        copy(self::SCHEMA, self::TESTSCHEMA);
        system("cp -r ".self::PUBLICS." ".self::WORK);
        $this->bbs = new BicBucStriim(self::DATADB,self::PUBLICP, true);
    }

    function tearDown() {
        // Must use nuke() to clear caches etc.
        R::nuke();
        R::close();
        $this->bbs = NULL;
        system("rm -rf ".self::WORK);
    }

    function testDbOk() {
        $this->assertTrue($this->bbs->dbOk());
        $this->bbs = new BicBucStriim(self::DATA.'/nodata.db',self::PUBLICP);
        $this->assertFalse($this->bbs->dbOk());
    }

    function testCreateDb() {
        $this->bbs = new BicBucStriim(self::DATA.'/nodata.db', self::PUBLICP);
        $this->assertFalse($this->bbs->dbOk());
        $this->bbs->createDataDB(self::DATA.'/newdata.db');
        $this->assertTrue(file_exists(self::DATA.'/newdata.db'));
        $this->bbs = new BicBucStriim(self::DATA.'/newdata.db', self::PUBLICP);
        $this->assertTrue($this->bbs->dbOk());
    }

    function testConfigs() {
        $configs = $this->bbs->configs();
        $this->assertEquals(1, count($configs));

        $configA = array('propa' => 'vala', 'propb' => 1);
        $this->bbs->saveConfigs($configA);
        $configs = $this->bbs->configs();
        $this->assertEquals(3, count($configs));
        $this->assertEquals('propa', $configs[2]->name);
        $this->assertEquals('vala', $configs[2]->val);
        $this->assertEquals('propb', $configs[3]->name);
        $this->assertEquals(1, $configs[3]->val);

        $configB = array('propa' => 'vala', 'propb' => 2);
        $this->bbs->saveConfigs($configB);
        $configs = $this->bbs->configs();
        $this->assertEquals(3, count($configs));
        $this->assertEquals('propa', $configs[2]->name);
        $this->assertEquals('vala', $configs[2]->val);
        $this->assertEquals('propb', $configs[3]->name);
        $this->assertEquals(2, $configs[3]->val);
    }

    function testAddUser() {
        $this->assertEquals(1, count($this->bbs->users()));
        $user = $this->bbs->addUser('testuser', 'testuser');
        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user->username);
        $this->assertNotEquals('testuser', $user->password);
        $this->assertNull($user->tags);
        $this->assertNull($user->languages);
        $this->assertEquals(0, $user->role);
    }

    function testAddUserEmptyUser() {
        $user = $this->bbs->addUser('', '');
        $this->assertNull($user);
    }

    function testAddUserEmptyUsername() {
        $user = $this->bbs->addUser('testuser2', '');
        $this->assertNull($user);
    }

    function testAddUserEmptyPassword() {
        $user = $this->bbs->addUser('', '');
        $this->assertNull($user);
    }

    function testGetUser() {
        $this->bbs->addUser('testuser', 'testuser');
        $this->bbs->addUser('testuser2', 'testuser2');
        $this->assertEquals(3, count($this->bbs->users()));
        $user = $this->bbs->user(3);
        $this->assertNotNull($user);
        $this->assertEquals('testuser2', $user->username);
        $this->assertNotEquals('testuser2', $user->password);
        $this->assertNull($user->tags);
        $this->assertNull($user->languages);
        $this->assertEquals(0, $user->role);
    }

    function testDeleteUser() {
        $this->bbs->addUser('testuser', 'testuser');
        $this->bbs->addUser('testuser2', 'testuser2');
        $this->assertEquals(3, count($this->bbs->users()));

        $deleted = $this->bbs->deleteUser(1);
        $this->assertFalse($deleted);

        $deleted = $this->bbs->deleteUser(100);
        $this->assertFalse($deleted);

        $deleted = $this->bbs->deleteUser(3);
        $this->assertTrue($deleted);
        $this->assertEquals(2, count($this->bbs->users()));
        $user = $this->bbs->user(2);
        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user->username);
    }

    function testChangeUser() {
        $this->bbs->addUser('testuser', 'testuser');
        $this->bbs->addUser('testuser2', 'testuser2');
        $users = $this->bbs->users();
        $password2 = $users[3]->password;

        $changed = $this->bbs->changeUser(3, $password2, 'deu', 'poetry', 'user');
        $this->assertEquals($password2, $changed->password);
        $this->assertEquals('deu', $changed->languages);
        $this->assertEquals('poetry', $changed->tags);

        $changed = $this->bbs->changeUser(3, 'new password', 'deu', 'poetry', 'user');
        $this->assertNotEquals($password2, $changed->password);
        $this->assertEquals('deu', $changed->languages);
        $this->assertEquals('poetry', $changed->tags);

        $changed = $this->bbs->changeUser(3, '', 'deu', 'poetry', 'user');
        $this->assertNull($changed);
    }

    function testChangeUserRole() {
        $this->bbs->addUser('testuser', 'testuser');
        $this->bbs->addUser('testuser2', 'testuser2');
        $users = $this->bbs->users();
        $password2 = $users[3]->password;

        $this->assertEquals('0', $users[3]->role);
        $changed = $this->bbs->changeUser(3, $password2, 'deu', 'poetry', 'admin');
        $this->assertEquals('1', $changed->role);
        $changed = $this->bbs->changeUser(3, '', 'deu', 'poetry', 'admin');
        $this->assertNull($changed);
    }

    function testIdTemplates() {
        $this->assertEquals(0, count($this->bbs->idTemplates()));
        $this->bbs->addIdTemplate('google', 'http://google.com/%id%', 'Google search');
        $this->bbs->addIdTemplate('amazon', 'http://amazon.com/%id%', 'Amazon search');
        $this->assertEquals(2, count($this->bbs->idTemplates()));
        $template = $this->bbs->idTemplate('amazon');
        $this->assertEquals('amazon', $template->name);
        $this->assertEquals('http://amazon.com/%id%', $template->val);
        $this->assertEquals('Amazon search', $template->label);
    }

    function testDeleteIdTemplates() {
        $this->assertEquals(0, count($this->bbs->idTemplates()));
        $this->bbs->addIdTemplate('google', 'http://google.com/%id%', 'Google search');
        $this->bbs->addIdTemplate('amazon', 'http://amazon.com/%id%', 'Amazon search');
        $this->assertEquals(2, count($this->bbs->idTemplates()));
        $this->bbs->deleteIdTemplate('amazon123');
        $this->assertEquals(2, count($this->bbs->idTemplates()));
        $this->bbs->deleteIdTemplate('amazon');
        $this->assertEquals(1, count($this->bbs->idTemplates()));
    }

    function testChangeIdTemplate() {
        $this->assertEquals(0, count($this->bbs->idTemplates()));
        $this->bbs->addIdTemplate('google', 'http://google.com/%id%', 'Google search');
        $this->bbs->addIdTemplate('amazon', 'http://amazon.com/%id%', 'Amazon search');
        $this->assertEquals(2, count($this->bbs->idTemplates()));
        $template = $this->bbs->idTemplate('amazon');
        $this->assertEquals('amazon', $template->name);
        $this->assertEquals('http://amazon.com/%id%', $template->val);
        $this->assertEquals('Amazon search', $template->label);
        $template = $this->bbs->changeIdTemplate('amazon', 'http://amazon.de/%id%', 'Amazon DE search');
        $this->assertEquals('amazon', $template->name);
        $this->assertEquals('http://amazon.de/%id%', $template->val);
        $this->assertEquals('Amazon DE search', $template->label);
    }

    function testCalibreThing() {
        $this->assertNull($this->bbs->getCalibreThing(DataConstants::CALIBRE_AUTHOR_TYPE, 1));
        $result = $this->bbs->addCalibreThing(DataConstants::CALIBRE_AUTHOR_TYPE, 1, 'Author 1');
        $this->assertNotNull($result);
        $this->assertEquals('Author 1', $result->cname);
        $this->assertEquals(0, $result->refctr);
        $result2 = $this->bbs->getCalibreThing(DataConstants::CALIBRE_AUTHOR_TYPE, 1);
        $this->assertEquals('Author 1', $result2->cname);
        $this->assertEquals(0, $result2->refctr);
    }

    function testEditAuthorThumbnail() {
        $this->assertTrue($this->bbs->editAuthorThumbnail(1, 'Author Name', true, self::AUTHOR1, 'image/jpeg'));
        $this->assertTrue(file_exists(self::THUMBA.'/author_1_thm.png'));
        $result2 = $this->bbs->getCalibreThing(DataConstants::CALIBRE_AUTHOR_TYPE, 1);
        $this->assertEquals('Author Name', $result2->cname);
        $this->assertEquals(1, $result2->refctr);
        $artefacts = $result2->ownArtefact;
        $this->assertEquals(1, count($artefacts));
        $result = $artefacts[1];
        $this->assertNotNull($result);
        $this->assertEquals(DataConstants::AUTHOR_THUMBNAIL_ARTEFACT, $result->atype);
        $this->assertEquals(realpath(self::THUMBA.'/author_1_thm.png'), $result->url);
    }

    function testGetAuthorThumbnail() {
        $this->assertTrue($this->bbs->editAuthorThumbnail(1, 'Author Name', true, self::AUTHOR1, 'image/jpeg'));
        $this->assertTrue($this->bbs->editAuthorThumbnail(2, 'Author Name', true, self::AUTHOR1, 'image/jpeg'));
        $result = $this->bbs->getAuthorThumbnail(1);
        $this->assertNotNull($result);
        $this->assertEquals(DataConstants::AUTHOR_THUMBNAIL_ARTEFACT, $result->atype);
        $this->assertEquals(realpath(self::THUMBA.'/author_1_thm.png'), $result->url);
        $result = $this->bbs->getAuthorThumbnail(2);
        $this->assertNotNull($result);
    }

    function testDeleteAuthorThumbnail() {
        $this->assertTrue($this->bbs->editAuthorThumbnail(1, 'Author Name', true, self::AUTHOR1, 'image/jpeg'));
        $this->assertNotNull($this->bbs->getAuthorThumbnail(1));
        $this->assertTrue($this->bbs->deleteAuthorThumbnail(1));
        $this->assertFalse(file_exists(self::THUMBA.'/author_1_thm.png'));
        $this->assertNull($this->bbs->getAuthorThumbnail(1));
        $this->assertEquals(0, R::count('artefact'));
        $this->assertEquals(0, R::count('calibrething'));
    }

    function testAuthorLinks() {
        $this->assertEquals(0, count($this->bbs->authorLinks(1)));
        $this->bbs->addAuthorLink(2, 'Author 1', 'google', 'http://google.com/1');
        $this->bbs->addAuthorLink(1, 'Author 2', 'amazon', 'http://amazon.com/2');
        $links = $this->bbs->authorLinks(1);
        $this->assertEquals(2, R::count('link'));
        $this->assertEquals(1, count($links));
        $this->assertEquals(DataConstants::AUTHOR_LINK, $links[0]->ltype);
        $this->assertEquals('amazon', $links[0]->label);
        $this->assertEquals('http://amazon.com/2', $links[0]->url);
        $this->assertEquals(2, $links[0]->id);
        $this->assertTrue($this->bbs->deleteAuthorLink(1, 2));
        $this->assertEquals(0, count($this->bbs->authorLinks(1)));
        $this->assertEquals(1, R::count('link'));
    }

    function testAuthorNote() {
        $this->assertNull($this->bbs->authorNote(1));
        $this->bbs->editAuthorNote(2, 'Author 1', 'text/plain', 'Goodbye, goodbye!');
        $this->bbs->editAuthorNote(1, 'Author 2', 'text/plain', 'Hello again!');
        $this->assertEquals(2, R::count('note'));
        $note = $this->bbs->authorNote(1);
        $this->assertNotNull($note);
        $this->assertEquals(DataConstants::AUTHOR_NOTE, $note->ntype);
        $this->assertEquals('text/plain', $note->mime);
        $this->assertEquals('Hello again!', $note->ntext);
        $this->assertEquals(2, $note->id);
        $note = $this->bbs->editAuthorNote(1, 'Author 2', 'text/markdown', '*Hello again!*');
        $this->assertEquals('text/markdown', $note->mime);
        $this->assertEquals('*Hello again!*', $note->ntext);
        $this->assertTrue($this->bbs->deleteAuthorNote(1, 2));
        $this->assertEquals(1, R::count('note'));
    }

    function testIsTitleThumbnailAvailable() {
        $this->assertNotNull($this->bbs->titleThumbnail(1, self::AUTHOR1, true));
        $this->assertTrue($this->bbs->isTitleThumbnailAvailable(1));
        $this->assertFalse($this->bbs->isTitleThumbnailAvailable(2));
    }


    function testClearThumbnail() {
        $result = $this->bbs->titleThumbnail(3, self::AUTHOR1, true);
        $this->assertNotNull($result);
        $this->assertTrue($this->bbs->isTitleThumbnailAvailable(3));
        $this->assertTrue($this->bbs->clearThumbnails());
        clearstatcache(true);
        $this->assertFalse(file_exists($result));
    }

}
?>

