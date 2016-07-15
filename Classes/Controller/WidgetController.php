<?php

namespace T3G\AgencyPack\Blog\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use T3G\AgencyPack\Blog\Domain\Repository\CategoryRepository;
use T3G\AgencyPack\Blog\Domain\Repository\CommentRepository;
use T3G\AgencyPack\Blog\Domain\Repository\PostRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Widget related controller.
 */
class WidgetController extends ActionController
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param PostRepository $postRepository
     */
    public function injectPostRepository(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param CommentRepository $commentRepository
     */
    public function injectCommentRepository(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     *
     */
    public function categoriesAction()
    {
        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     *
     */
    public function recentPostsAction()
    {
        $this->view->assign('posts', $this->postRepository->findAll());
    }

    /**
     *
     */
    public function commentsAction()
    {
        $limit = (int) $this->settings['widgets']['comments']['limit'] ?: 5;
        $this->view->assign('comments', $this->commentRepository->findLatest($limit));
    }

    /**
     *
     */
    public function archiveAction()
    {
        $this->view->assign('archiveData', $this->resortArchiveData(
            $this->postRepository->findMonthsAndYearsWithPosts()
        ));
    }

    /**
     * This method resort the database result and create a nested array
     * in the form:
     * [
     *  2015 => [
     *    [
     *      'year' => 2015,
     *      'month' => 3,
     *      'count' => 9
     *      'timestamp' => 123456789
     *    ]
     *    ...
     *  ]
     *  ...
     * ].
     *
     * @param array $data
     *
     * @return array
     */
    protected function resortArchiveData(array $data)
    {
        $archiveData = array();
        foreach ($data as $result) {
            if (empty($archiveData[$result['year']])) {
                $archiveData[$result['year']] = array();
            }
            $result['timestamp'] = mktime(0, 0, 0, (int) $result['month'], 1, (int) $result['year']);
            $archiveData[$result['year']][] = $result;
        }

        return $archiveData;
    }
}
