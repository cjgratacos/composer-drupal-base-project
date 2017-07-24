<?php
namespace Drupal\cjgratacos\entity_test\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\cjgratacos\entity_test\Course\CourseInterface;

/**
 * Class Course
 * @package Drupal\cjgratacos\entity_test\Entity
 * @ingroup entity_test
 *
 * @ContentEntityType(
 *     id = "entity_test_course",
 *     label = @Translation("Course Entity"),
 *     handler = {
 *      "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *      "list_builder" = "Drupal\cjgratacos\test_entity\Entity\Controller\CourseListBuilder",
 *      "form" = {
 *              "add" = "Drupal\cjgratacos\test_entity\Form\CourseForm",
 *              "edit" = "Drupal\cjgratacos\test_entity\Form\CourseForm",
 *              "delete" =  "Drupal\cjgratacos\test_entity\Form\CourseDeleteForm"
 *         },
 *      "access" = "Drupal\cjgratacos\test_entity\AccessController\CourseAccessControlHandler"
 *     },
 *     list_cache_contexts = { "user" },
 *     base_table = "course",
 *     admin_permissions = "administer entity_test entity",
 *     entity_keys = {
 *          "id" = "id",
 *          "label" = "name",
 *          "uuid" = "uuid"
 *     },
 *     links = {
 *          "canonical" = "/entity_test/{entity_test_course}",
 *          "edit-form" = "/entity_test/{entity_test_course}/edit",
 *          "delete-form" = "/entity_test/{entity_test_course}/delete",
 *          "collection" = "/entity_test/list"
 *     },
 *     field_ui_base_route = "entity_test.course_settings",
 * )
 */
class Course extends ContentEntityBase implements CourseInterface
{

    public function __construct(array $values, $entity_type, $bundle = FALSE, array $translations = [])
    {
        parent::__construct($values, $entity_type, $bundle, $translations);
    }

    public function sayHello(): string
    {
     return "This is working!!!!!!!";
    }

    public function hello(): string {
      return "WOooooooooWWWWWW!!!!";
    }
}
