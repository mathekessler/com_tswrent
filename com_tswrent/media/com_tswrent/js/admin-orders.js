/**
 * Handles the change event on the order state select list.
 *
 * @param {Event} event The DOM event.
 */
window.updateOrderState = async (event) => {
  const select = event.target;
  const { value: orderstate } = select;
  const { orderid: id } = select.dataset;
  const token = Joomla.getOptions('csrf.token');

  if (!id || !token) {
    console.error('Order ID or CSRF token is missing.');
    return;
  }

  const formData = new FormData();
  formData.append('id', id);
  formData.append('orderstate', orderstate);
  formData.append(token, '1');

  try {
    const response = await fetch(
      'index.php?option=com_tswrent&task=order.updateState&format=json',
      { method: 'POST', body: formData },
    );

    const result = await response.json();

    if (result.success) {
      // Visual feedback for success
      select.style.border = '2px solid green';
      setTimeout(() => { select.style.border = ''; }, 1000);
      // Use renderMessage instead of renderMessages to avoid the "u is null" error
      if (result.messages && Array.isArray(result.messages)) {
        result.messages.forEach((message) => Joomla.renderMessage('success', message));
      }
    } else {
      throw new Error(result.message || 'Unknown error');
    }
  } catch (err) {
    Joomla.renderMessages({ error: [err.message] });
    // Visual feedback for error
    select.style.border = '2px solid red';
    setTimeout(() => { select.style.border = ''; }, 2000);
  }
};
