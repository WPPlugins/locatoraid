<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Snippet_Appender_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $title;
	protected $snippet;
	protected $append_to;

	public function render()
	{
		$snippet = $this->snippet();
		$append_to = $this->append_to();
		$title = $this->title();
		$title_id = 'hc_' . HC_Lib2::generate_rand();
		$title
			->add_attr('id', $title_id)
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$title->add_attr( $k, $v );
		}

		$template_id = 'hc_' . HC_Lib2::generate_rand();
		$template = $this->make('/html/view/element')
			->tag('script')
			->add_attr('type', 'text/template')
			->add_attr('id', $template_id)
			->add( $snippet )
			;

		$js = $this->js( $title_id, $template_id, $append_to );

		$out = $this->make('/html/view/container');
		$out
			->add( $title )
			->add( $js )
			->add( $template )
			;
		return $out;
	}

	public function js( $title_id, $template_id, $append_to )
	{
		$js = <<<EOT

<script type="text/javascript">

jQuery(document).on( 'click', '#$title_id', function(event)
{
	var append_to = jQuery('#$append_to');

	var tbody = append_to.find('tbody');
	if( tbody.length ){
		append_to = tbody;
	}
	append_to.append( jQuery('#$template_id').html() );
	return false;
});

</script>

EOT;

		return $js;
	}


	public function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	public function title()
	{
		return $this->title;
	}
	public function set_append_to( $append_to )
	{
		$this->append_to = $append_to;
		return $this;
	}
	public function append_to()
	{
		return $this->append_to;
	}
	public function set_snippet( $snippet )
	{
		$this->snippet = $snippet;
		return $this;
	}
	public function snippet()
	{
		return $this->snippet;
	}
}