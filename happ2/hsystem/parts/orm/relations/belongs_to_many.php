<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC_ORM_Relation_Belongs_To_Many extends HC_ORM_Relation_Has_Many
{
	protected $type = 'belongs_to_many';

	protected $my_id_field		= 'from_id';
	protected $their_id_field	= 'to_id';
}