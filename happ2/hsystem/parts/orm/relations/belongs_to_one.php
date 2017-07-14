<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC_ORM_Relation_Belongs_To_One extends HC_ORM_Relation_Has_One
{
	protected $type = 'belongs_to_one';

	protected $my_id_field		= 'from_id';
	protected $their_id_field	= 'to_id';
}