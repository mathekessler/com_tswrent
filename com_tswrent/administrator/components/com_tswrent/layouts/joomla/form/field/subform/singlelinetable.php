<?php
defined('_JEXEC') or die;

/** @var array $displayData */
extract($displayData);

$forms = is_array($forms) ? $forms : (array) $forms;
?>

<table class="table table-striped">
    <thead>
        <tr>
            <?php if (!empty($forms) && isset($forms[0]) && $forms[0] instanceof Joomla\CMS\Form\Form) : ?>
                <?php foreach ($forms[0]->getFieldset() as $field) : ?>
                    <th><?php echo $field->label; ?></th>
                <?php endforeach; ?>
                <th>Löschen</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($forms as $itemForm) : ?>
            <?php if ($itemForm instanceof Joomla\CMS\Form\Form) : ?>
                <tr>
                    <?php foreach ($itemForm->getFieldset() as $field) : ?>
                        <td>
                            <?php
                            $value = $itemForm->getValue($field->fieldname);

                            if (is_array($value)) {
                                echo htmlspecialchars(json_encode($value));
                            } else {
                                echo htmlspecialchars((string) $value);
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button type="button" class="btn btn-danger"
                            onclick="Joomla.subform.remove(this)">
                            Löschen
                        </button>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<button type="button" class="btn btn-success" onclick="Joomla.subform.add(this)">
    Produkt hinzufügen
</button>
