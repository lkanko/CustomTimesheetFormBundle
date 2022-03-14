<?php

/*
 * This file is part of the CustomTimesheetFormBundle for Kimai 2.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomTimesheetFormBundle\Controller;

use App\Controller\TimesheetController as TimesheetBaseController;
use App\Entity\Timesheet;
use App\Event\TimesheetMetaDisplayEvent;
use App\Export\ServiceExport;
use App\Repository\ActivityRepository;
use App\Repository\ProjectRepository;
use App\Repository\TagRepository;
use KimaiPlugin\CustomTimesheetFormBundle\Form\TimesheetEditForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/timesheet")
 * @Security("is_granted('view_own_timesheet')")
 */
class TimesheetController extends TimesheetBaseController
{
    /**
     * @Route(path="/", defaults={"page": 1}, name="timesheet", methods={"GET"})
     * @Route(path="/page/{page}", requirements={"page": "[1-9]\d*"}, name="timesheet_paginated", methods={"GET"})
     * @Security("is_granted('view_own_timesheet')")
     *
     * @param int $page
     * @param Request $request
     * @return Response
     */
    public function indexAction(int $page, Request $request): Response
    {
        $query = $this->createDefaultQuery();
        $query->setPage($page);
        return $this->index($query, $request, 'timesheet', 'timesheet/index.html.twig', TimesheetMetaDisplayEvent::TIMESHEET);
    }

    /**
     * @Route(path="/export/", name="timesheet_export", methods={"GET", "POST"})
     * @Security("is_granted('export_own_timesheet')")
     *
     * @param Request $request
     * @param ServiceExport $serviceExport
     * @return Response
     */
    public function exportAction(Request $request, ServiceExport $serviceExport): Response
    {
        return $this->export($request, $serviceExport);
    }

    /**
     * @Route(path="/{id}/edit", name="timesheet_edit", methods={"GET", "POST"})
     * @Security("is_granted('edit', entry)")
     *
     * @param Timesheet $entry
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(Timesheet $entry, Request $request): Response
    {
        return $this->edit($entry, $request, '@CustomTimesheetForm/timesheet/edit.html.twig');
    }

    /**
     * @Route(path="/{id}/duplicate", name="timesheet_duplicate", methods={"GET", "POST"})
     * @Security("is_granted('duplicate', entry)")
     */
    public function duplicateAction(Timesheet $entry, Request $request): Response
    {
        return $this->duplicate($entry, $request, '@CustomTimesheetForm/timesheet/edit.html.twig');
    }

    /**
     * @Route(path="/multi-update", name="timesheet_multi_update", methods={"POST"})
     * @Security("is_granted('edit_own_timesheet')")
     */
    public function multiUpdateAction(Request $request): Response
    {
        return $this->multiUpdate($request, 'timesheet/multi-update.html.twig');
    }

    /**
     * @Route(path="/multi-delete", name="timesheet_multi_delete", methods={"POST"})
     * @Security("is_granted('delete_own_timesheet')")
     */
    public function multiDeleteAction(Request $request): Response
    {
        return $this->multiDelete($request);
    }

    /**
     * @Route(path="/create", name="timesheet_create", methods={"GET", "POST"})
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param ActivityRepository $activityRepository
     * @param TagRepository $tagRepository
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, ProjectRepository $projectRepository, ActivityRepository $activityRepository, TagRepository $tagRepository): Response
    {
        return $this->create($request, '@CustomTimesheetForm/timesheet/edit.html.twig', $projectRepository, $activityRepository, $tagRepository);
    }

    protected function getCreateForm(Timesheet $entry): FormInterface
    {
        return $this->generateCreateForm($entry, TimesheetEditForm::class, $this->generateUrl('timesheet_create'));
    }

    protected function getDuplicateForm(Timesheet $entry, Timesheet $original): FormInterface
    {
        return $this->generateCreateForm($entry, TimesheetEditForm::class, $this->generateUrl('timesheet_duplicate', ['id' => $original->getId()]));
    }

    protected function getCreateFormClassName(): string
    {
        return TimesheetEditForm::class;
    }

    protected function getEditFormClassName(): string
    {
        return TimesheetEditForm::class;
    }
}
