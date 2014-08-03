<?php
/**
 * Openstep Media
 * http://www.openstepmedia.com
 *
 * Create Configurable Product Settings Tab Block
 * This module is useful when there are multiple attributes in an attribute set
 * where the attributes may share a similar name.  This module will add the
 * attribute_code and attribute_id to the label for the attributes at the time
 * when you are first creating a new configurable product.  To see the effects
 * of this module go to:
 * Catalog -> Manage Products -> New Product -> Configurable Product
 *
 * @category   Mage
 * @package    Openstepmedia_AdminConfigurableProductAtt_Adminhtml
 * @author     Seth Markowitz <seth@openstepmedia.com>
 */
class Openstepmedia_AdminConfigurableProductSettings_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array(
            'legend'=>Mage::helper('catalog')->__('Select Configurable Attributes ')
        ));

        $product    = $this->_getProduct();
        $attributes = $product->getTypeInstance(true)
            ->getSetAttributes($product);

        $fieldset->addField('req_text', 'note', array(
            'text' => '<ul class="messages"><li class="notice-msg"><ul><li>'
                    .  $this->__('Only attributes with scope "Global", input type "Dropdown" and Use To Create Configurable Product "Yes" are available.')
                    . '</li></ul></li></ul>'
        ));

        $hasAttributes = false;

        foreach ($attributes as $attribute) {
            if ($product->getTypeInstance(true)->canUseAttribute($attribute, $product)) {
                $hasAttributes = true;
                $codeText = " [" . $attribute->getAttributeId() . ":" . $attribute->getAttributeCode() . "]";
                $editUrl = Mage::getModel('adminhtml/url')->getUrl('*/catalog_product_attribute/edit', array('attribute_id' => $attribute->getAttributeId()));
                $editUrlTitle = $this->__('Edit Attribute');
                $codeHtml = " <i class='catalog-product-edit-tab-super-settings-label'>[<a href='$editUrl' title='$editUrlTitle' target='_blank'><span class='attribute-id'>" . $attribute->getAttributeId() . "</span>:<span class='attribute-code'>" . $attribute->getAttributeCode() . "</a>]</span></i>";
                $fieldset->addField('attribute_'.$attribute->getAttributeId(), 'checkbox', array(
                    'label' => $attribute->getFrontend()->getLabel() . $codeHtml,
                    'title' => $attribute->getFrontend()->getLabel() . $codeText,
                    'name'  => 'attribute',
                    'class' => 'attribute-checkbox',
                    'value' => $attribute->getAttributeId()
                ));
            }
        }

        if ($hasAttributes) {
            $fieldset->addField('attributes', 'hidden', array(
                        'name'  => 'attribute_validate',
                        'value' => '',
                        'class' => 'validate-super-product-attributes'
                    ));

            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }
        else {
            $fieldset->addField('note_text', 'note', array(
                'text' => $this->__('This attribute set does not have attributes which we can use for configurable product')
            ));
            $fieldset->addField('back_button', 'note', array(
                'text' => $this->getChildHtml('back_button'),
            ));
        }


        $this->setForm($form);

        //return parent::_prepareForm();
    }

}
