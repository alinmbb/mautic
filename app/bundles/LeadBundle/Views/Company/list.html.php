<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('MauticLeadBundle:Company:index.html.php');
}
?>

<?php if (count($items)): ?>
    <div class="table-responsive page-list">
        <table class="table table-hover table-striped table-bordered company-list" id="companyTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#companyTable',
                        'routeBase'       => 'company',
                        'templateButtons' => [
                            'delete' => $permissions['lead:leads:deleteother'],
                        ],
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'company',
                        'text'       => 'mautic.company.name',
                        'class'      => 'col-company-name',
                        'orderBy'    => 'comp.companyname',
                    ]
                );
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'company',
                        'text'       => 'mautic.company.email',
                        'class'      => 'visible-md visible-lg col-company-category',
                        'orderBy'    => 'comp.companyemail',
                    ]
                );
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'company',
                        'text'       => 'mautic.company.website',
                        'class'      => 'visible-md visible-lg col-company-website',
                        'orderBy'    => 'comp.companywebsite',
                    ]
                );
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'company',
                        'text'       => 'mautic.company.score',
                        'class'      => 'visible-md visible-lg col-company-score',
                        'orderBy'    => 'comp.score',
                    ]
                );
                echo $view->render('MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'company',
                        'text'       => 'mautic.lead.list.thead.leadcount',
                        'class'      => 'visible-md visible-lg col-leadlist-leadcount',
                    ]
                );
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'company',
                        'orderBy'    => 'comp.id',
                        'text'       => 'mautic.core.id',
                        'class'      => 'visible-md visible-lg col-company-id',
                    ]
                );
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <?php $fields = $item->getFields(); ?>
                <tr>
                    <td>
                        <?php
                        echo $view->render(
                            'MauticCoreBundle:Helper:list_actions.html.php',
                            [
                                'item'            => $item,
                                'templateButtons' => [
                                    'edit'   => $permissions['lead:leads:editother'],
                                    'clone'  => $permissions['lead:leads:create'],
                                    'delete' => $permissions['lead:leads:deleteother'],
                                ],
                                'routeBase' => 'company',
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <div>
                            <?php if ($view['security']->hasEntityAccess(
                                       $permissions['lead:leads:editown'],
                                       $permissions['lead:leads:editother'],
                                       $item->getCreatedBy()
                                       )
                                   ): ?>

                            <a href="<?php echo $view['router']->url(
                                'mautic_company_action',
                                ['objectAction' => 'view', 'objectId' => $item->getId()]
                            ); ?>" data-toggle="ajax">
                                <?php if (isset($fields['core']['companyname'])) : ?>
                                    <?php echo $view->escape($fields['core']['companyname']['value']); ?>
                                <?php endif; ?>
                            </a>
                        <?php else: ?>
                            <?php if (isset($fields['core']['companyname'])) : ?>
                                <?php echo $view->escape($fields['core']['companyname']['value']); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if (isset($fields['core']['companyemail'])): ?>
                        <div class="text-muted mt-4">
                            <small>
                                <?php echo $view->escape($fields['core']['companyemail']['value']); ?>
                            </small>
                        </div>
                        <?php endif; ?>
                    </td>

                    <td class="visible-md visible-lg">
                        <?php if (isset($fields['core']['companywebsite'])) :?>
                        <?php echo \Mautic\CoreBundle\Helper\InputHelper::url($fields['core']['companywebsite']['value']); ?>
                        <?php endif; ?>
                    </td>
                    <td class="visible-md visible-lg">
                        <?php echo $item->getScore(); ?>
                    </td>
                    <td class="visible-md visible-lg">
                        <a class="label label-primary" href="<?php
                        echo $view['router']->path(
                            'mautic_contact_index',
                            [
                                'search' => $view['translator']->trans('mautic.lead.lead.searchcommand.company_id').':'.$item->getId(),
                            ]
                        ); ?>" data-toggle="ajax"<?php echo (0 == $leadCounts[$item->getId()]) ? 'disabled=disabled' : ''; ?>>
                            <?php echo $view['translator']->transChoice(
                                'mautic.lead.company.viewleads_count',
                                $leadCounts[$item->getId()],
                                ['%count%' => $leadCounts[$item->getId()]]
                            ); ?>
                        </a>
                    </td>
                    <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <?php echo $view->render(
            'MauticCoreBundle:Helper:pagination.html.php',
            [
                'totalItems' => $totalItems,
                'page'       => $page,
                'limit'      => $limit,
                'menuLinkId' => 'mautic_company_index',
                'baseUrl'    => $view['router']->url('mautic_company_index'),
                'sessionVar' => 'company',
            ]
        ); ?>
    </div>
<?php else: ?>
    <?php echo $view->render(
        'MauticCoreBundle:Helper:noresults.html.php',
        ['tip' => 'mautic.company.action.noresults.tip']
    ); ?>
<?php endif; ?>
