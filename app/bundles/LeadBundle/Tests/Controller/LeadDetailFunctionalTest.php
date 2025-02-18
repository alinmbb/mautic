<?php

declare(strict_types=1);

namespace Mautic\LeadBundle\Tests\Controller;

use Doctrine\DBAL\FetchMode;
use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\LeadField;
use PHPUnit\Framework\Assert;

class LeadDetailFunctionalTest extends MauticMysqlTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        defined('MAUTIC_TABLE_PREFIX') or define('MAUTIC_TABLE_PREFIX', '');
    }

    public function testCustomFieldOrderIsRespected(): void
    {
        $lead = new Lead();
        $this->em->persist($lead);

        $fieldRepository = $this->em->getRepository(LeadField::class);

        /** @var LeadField[] $fields */
        $fields = $fieldRepository->findBy(['object' => 'lead', 'group' => 'core'], [
            'label' => 'desc',
            'id'    => 'desc',
        ]);
        $order = 0;

        // re-order fields by the label
        foreach ($fields as $field) {
            $field->setOrder(++$order);
            $this->em->persist($field);
        }

        $this->em->flush();
        $this->em->clear();

        // get expected core labels
        $expectedLabels = $this->connection->createQueryBuilder()
            ->select('label')
            ->from(MAUTIC_TABLE_PREFIX.'lead_fields')
            ->where('object = "lead"')
            ->andWhere('field_group = "core"')
            ->orderBy('field_order')
            ->execute()
            ->fetchAll(FetchMode::COLUMN);

        $crawler = $this->client->request('GET', sprintf('/s/contacts/view/%d', $lead->getId()));

        // get actual core labels
        $actualLabels = $crawler->filter('#lead-details table')
            ->first()
            ->filter('td:first-child')
            ->extract(['_text']);
        $actualLabels = array_map('trim', $actualLabels);

        Assert::assertSame($expectedLabels, $actualLabels);
    }

    public function testLeadViewPreventsXSS(): void
    {
        $firstName = 'aaa" onmouseover=alert(1) a="';
        $lead      = new Lead();
        $lead->setFirstname($firstName);
        $this->em->persist($lead);
        $this->em->flush();
        $this->em->clear();

        $crawler = $this->client->request('GET', sprintf('/s/contacts/view/%d', $lead->getId()));

        $anchorTag  = $crawler->filter('#toolbar ul.dropdown-menu-right li')->first()->filter('a');
        $mouseOver  = $anchorTag->attr('onmouseover');
        $dataHeader = $anchorTag->attr('data-header');

        Assert::assertNull($mouseOver);
        Assert::assertSame(sprintf('Campaigns %s is part of', $firstName), $dataHeader);
    }
}
