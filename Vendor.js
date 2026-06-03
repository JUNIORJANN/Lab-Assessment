fetch("Vendor.php")
    .then(res => res.json())
    .then(data => {
        const grid = document.getElementById("vendors");
        if (!grid) return;
        data.forEach(vendor => {
            const card = `
                <div class="vendor-card">
                    <img src="images/vendors/${vendor.image}" alt="${vendor.vendor_name}">
                    <h4>${vendor.vendor_name}</h4>
                    <p>${vendor.description}</p>
                    <a href="VendorStore.php?id=${vendor.id}" class="view-btn">Visit Store</a>
                </div>
            `;
            grid.innerHTML += card;
        });
    });