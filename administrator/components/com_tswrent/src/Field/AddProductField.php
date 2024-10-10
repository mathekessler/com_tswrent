<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TSWEB\Component\Tswrent\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ModalSelectField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\ParameterType;

/**
 * Custom Form Field Add Button that extends the ModalSelectField.
 */
class AddProductField extends ModalSelectField
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'AddProduct';

    /**
     * The modal ID to be used for the modal dialog.
     *
     * @var  string
     */
    protected $modalId = 'addModal';

    /**
     * The URL that will be loaded in the modal dialog.
     *
     * @var  string
     */
    protected $url = 'index.php?option=com_tswrent&view=products&layout=modal&tmpl=component';

    /**
     * The modal title.
     *
     * @var  string
     */
    protected $title = 'COM_YOURCOMPONENT_ADD_BUTTON_TITLE';
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
       // Get the field
       $result = parent::setup($element, $value, $group);

		if (!$result)
		{
			return $result;
		}

		$app = Factory::getApplication();

		// We need the Url to get the list of products, 
		// getting an editing form, a creation form
		// entities. We indicate them here.
		// The result of accessing these URLs should return HTML,
		// which will include a small javascript,
		// transmitting the selected values - product id and product name.

      
		$urlSelect = (new Uri())->setPath(Uri::base(true) . '/index.php');
		$urlSelect->setQuery([
			'option'                => 'com_tswrent',
            'view'                  => 'products',
            'layout'                => 'modal',
			'tmpl'                  => 'component',
			Session::getFormToken() => 1,
		]);

		$modalTitle = Text::_('COM_TSWEB_SUPPLIERS');
		$this->urls['select'] = (string) $urlSelect;

		// We comment on these lines, they are not needed. In the articles about JavaScript section
		// I’ll tell you why.
		// $wa = $app->getDocument()->getWebAssetManager();
		// $wa->useScript('field.modal-fields')->useScript('core');
		
		// Modal window title
		// To create and edit, respectively, you also need
		// individual headings
		$this->modalTitles['select'] = $modalTitle;

		// hint - the field placeholder in HTML.
		$this->hint = $this->hint ?: Text::_('COM_TSWEB_SUPPLIERS_CHOOSE_SUPPLIER');

		return $result;
	}
    

    /**
     * Method to generate the modal HTML.
     *
     * @return string  The modal HTML markup.
     */
    protected function getModalHtml()
    {
        $html = [];
        
        $html[] = '<div class="modal fade" id="' . $this->modalId . '" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">';
        $html[] = '  <div class="modal-dialog">';
        $html[] = '    <div class="modal-content">';
        $html[] = '      <div class="modal-header">';
        $html[] = '        <h5 class="modal-title" id="modalLabel">' . JText::_($this->title) . '</h5>';
        $html[] = '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        $html[] = '      </div>';
        $html[] = '      <div class="modal-body">';
        $html[] = '        <iframe src="' . $this->url . '" style="width:100%;height:400px;border:none;"></iframe>';
        $html[] = '      </div>';
        $html[] = '      <div class="modal-footer">';
        $html[] = '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . JText::_('JTOOLBAR_CLOSE') . '</button>';
        $html[] = '      </div>';
        $html[] = '    </div>';
        $html[] = '  </div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
