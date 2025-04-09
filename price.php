<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Laundry Price List</title>
  <link rel="stylesheet" href="price.css">
</head>
<body>

<h1>LaundryMate Price List</h1>

<div class="tabs" id="categoryTabs"></div>
<div id="priceList"></div>

<script>
let pricesByCategory = {};

fetch('get_price.php')
  .then(res => res.json())
  .then(data => {
    // Group by category
    data.forEach(item => {
      if (!pricesByCategory[item.category]) {
        pricesByCategory[item.category] = [];
      }
      pricesByCategory[item.category].push(item);
    });

    renderTabs();
    renderItems('MEN');
  });

function renderTabs() {
  const tabsContainer = document.getElementById('categoryTabs');
  tabsContainer.innerHTML = '';

  Object.keys(pricesByCategory).forEach(category => {
    const btn = document.createElement('button');
    btn.innerText = category;
    btn.onclick = () => {
      document.querySelectorAll('.tabs button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderItems(category);
    };
    if (category === 'MEN') btn.classList.add('active');
    tabsContainer.appendChild(btn);
  });
}

function renderItems(category) {
  const listContainer = document.getElementById('priceList');
  listContainer.innerHTML = '';

  pricesByCategory[category].forEach(item => {
    const div = document.createElement('div');
    div.className = 'card';
    div.innerHTML = `<span>${item.name}</span><strong>₹${item.price}</strong>`;
    listContainer.appendChild(div);
  });
}
</script>

</body>
</html>
