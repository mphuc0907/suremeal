<?php /* Template Name: Find-a-dealer */ ?>
<?php
global $wpdb;
$dealers = $wpdb->get_results("SELECT * FROM wp_dealer ORDER BY id DESC");

$datetime_ny = new DateTime("now", new DateTimeZone("America/New_York"));
$current_time_ny = $datetime_ny->format("g:ia");
$current_timestamp = strtotime($current_time_ny);
$url = get_template_directory_uri();
get_header();
?>
<main class="bg-[#EEF0F6]">
    <section class="pt-6 pb-10">
        <div class="container">
            <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                <ol class="flex flex-wrap gap-3 items-center" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url() ?>" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name"><?php pll_e('Home') ?></span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php pll_e('Find a Dealer') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
            <div class="mt-4">
                <h1 class="text-heading-h2 text-[#191C1F]"><?php pll_e('Find a Dealer') ?></h1>
            </div>
        </div>
    </section>

    <style>
        .tab-item {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 8px 16px 12px 8px;
            border-bottom: 2px solid transparent;
            color: #6B7280;
            background: #FFF;
            border-radius: 0;
            cursor: pointer;
        }

        .tab-item:hover {
            font-weight: bold;
            color: #0E74BC;
            border-bottom: 2px solid #0E74BC;
        }

        .tab-item.active {
            font-weight: bold;
            color: #0E74BC;
            border-bottom: 2px solid #0E74BC;
        }

        .tab-item-view {
            display: flex;
            flex-direction: column;
            padding: 24px;
            padding-bottom: 20px;
            gap: 24px;
            background: #FFF;
        }

        .iframe-container {
            position: relative;
            width: 100%;
            padding-top: calc(29 / 74 * 100%);
            /* Giữ tỷ lệ 74:29 */
            /* max-height: 638px; */
        }

        .iframe-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
    <section class="pb-6 lg:pb-20">
        <div class="container">
            <div class="flex flex-col lg:flex-row gap-6">
                <div data-aos="fade-right" data-aos-duration="1500" class="w-full lg:w-[41%] lg:max-w-[553px]">
                    <div class="w-full rounded-xl bg-white">
                        <div class="flex items-center pt-2">
                            <div class="tab-item text-body-md-regular active">
                                <?php pll_e('Find a Dealer') ?>
                            </div>
                            <div class="tab-item text-body-md-regular">
                                <?php pll_e('Dealer near your location') ?>
                            </div>
                        </div>
                        <div class="tab-item-view">
                            <input type="text" id="dealer-search-input" class="home-search" placeholder="<?php pll_e('Search dealer name, zip code, or address') ?>">

                            <div class="w-full relative flex items-center or">
                                <hr class="divider">
                                <span class="flex-shrink mx-3 text-body-sm-regular text-neutral-500"><?php pll_e('Or') ?></span>
                                <hr class="divider">
                            </div>

                            <div class="flex flex-col gap-3">
                                <label class="input-label">
                                    <select id="state" class="input-field">
                                        <option value=""><?php pll_e('Select a state') ?></option>
                                    </select>
                                </label>
                                <label class="input-label">
                                    <select id="city" class="input-field" disabled>
                                        <option value=""><?php pll_e('Select a city') ?></option>
                                    </select>
                                </label>
                            </div>

                            <div id="dealer-results" class="p-5 flex flex-col gap-6 rounded-xl border border-solid border-neutral-200">
                                <p id="dealer-count" class="text-body-md-medium text-gray-9"><?php pll_e('Showing all dealerships') ?></p>

                                <div id="dealer-list">
                                    <?php
                                    $post_count = 0;
                                    if ($dealers) :
                                        foreach ($dealers as $dealer) :
                                            $post_count++;
                                    ?>
                                            <label class="dealer-item <?= $post_count > 6 ? 'hidden' : '' ?> cursor-pointer py-4 flex gap-4 items-center border-b border-solid border-neutral-200"
                                                data-name="<?= esc_attr($dealer->dealer_name) ?>"
                                                data-address="<?= esc_attr($dealer->address) ?>"
                                                data-state="<?= esc_attr($dealer->state) ?>"
                                                data-city="<?= esc_attr($dealer->city) ?>"
                                                data-zip="<?= esc_attr($dealer->zip_code) ?>"
                                                data-latitude="<?= esc_attr($dealer->latitude) ?>"
                                                data-longitude="<?= esc_attr($dealer->longitude) ?>">
                                                <input type="radio" name="bill" class="radio-blue">
                                                <div class="flex flex-col gap-1">
                                                    <h2 class="text-body-md-mdeium text-gray-8"><?= $dealer->dealer_name ?></h2>
                                                    <p class="text-body-md-regular text-neutral-500">
                                                        <?= $dealer->address ?>
                                                    </p>
                                                </div>
                                            </label>
                                        <?php
                                        endforeach;
                                    else: ?>
                                        <p class="text-center"><?php pll_e('Data is being updated') ?></p>
                                    <?php endif; ?>
                                </div>

                                <?php if ($post_count > 6) : ?>
                                    <button id="view-more" class="flex items-center gap-4">
                                        <div class="">
                                            <figure class="w-5 h-5 view-more-icon">
                                                <img src="<?= $url ?>/assets/image/icon/double-chev-down-20.svg" alt="icon">
                                            </figure>
                                        </div>
                                        <p class="view-more-text text-body-sm-regular text-gray-9"><?php pll_e('View more detail') ?></p>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-left" data-aos-duration="1500" class="w-full lg:w-[58%] lg:max-w-[783px] dealer-container">
                    <?php if ($dealers) : ?>
                        <?php foreach ($dealers as $index => $dealer) :
                            preg_match('/@([0-9.-]+),([0-9.-]+)/', $dealer->map, $match);
                            if ($match) {
                                $lat = $match[1];
                                $lng = $match[2];
                                $url_map = 'https://www.google.com/maps/place/?q=' . urlencode($dealer->address) . '&center=' . $lat . ',' . $lng . '&zoom=16';
                            } else {
                                $url_map = 'https://www.google.com/maps/place/?q=' . urlencode($dealer->address);
                            }
                            
                            $open_time = strtotime($dealer->open_at);
                            $close_time = strtotime($dealer->close_at);
                        ?>
                            <div class="w-full rounded-xl bg-white flex flex-col gap-5 p-6 rounded-xl <?= $index !== 0 ? 'hidden' : '' ?>">
                                <h2 class="text-heading-h6 text-gray-8"><?= $dealer->dealer_name ?></h2>
                                <div class="iframe-container">
                                    <?= stripcslashes($dealer->map) ?>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex-1 flex flex-col gap-3">
                                        <div class="flex items-center gap-4">
                                            <?php if ($current_timestamp < $open_time): ?>
                                                <p class="text-body-md-regular text-neutral-500"><?php pll_e('Open at') ?> <?= $dealer->open_at ?></p>
                                                <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                                <p class="text-body-md-semibold text-secondary"><?php pll_e('Close') ?></p>
                                            <?php elseif ($current_timestamp >= $open_time && $current_timestamp < $close_time): ?>
                                                <p class="text-body-md-semibold text-secondary"><?php pll_e('Open') ?></p>
                                                <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                                <p class="text-body-md-regular text-neutral-500"><?php pll_e('Closes at') ?> <?= $dealer->close_at ?></p>
                                            <?php else: ?>
                                                <p class="text-body-md-regular text-neutral-500"><?php pll_e('Open at') ?> <?= $dealer->open_at ?></p>
                                                <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                                <p class="text-body-md-semibold text-secondary"><?php pll_e('Close') ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <p class="text-body-md-regular text-neutral-500"><?php pll_e('Address') ?>:</p>
                                            <a href="#" class="text-body-md-regular text-gray-8">
                                                <?= $dealer->address ?>
                                            </a>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <p class="text-body-md-regular text-neutral-500"><?php pll_e('Phone number') ?>:</p>
                                            <a href="#" class="text-body-md-regular text-gray-8"><?= $dealer->phone ?></a>
                                        </div>
                                    </div>
                                    <a href="<?= $url_map ?>" target="_blank" class="button bg-primary text-body-md-semibold text-white" aria-label="See direction"><?php pll_e('See direction') ?></a>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php else: ?>
                        <p class="text-center"><?php pll_e('Data is being updated') ?></p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    get_template_part('template-parts/support-info');
    ?>
</main>
<?php get_footer() ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');
        const jsonUrl = "<?php echo get_template_directory_uri(); ?>/assets/state-and-city.json";
        // console.log(jsonUrl);
        

        // Fetch the JSON file and populate the states
        fetch(jsonUrl)
            .then(response => response.json())
            .then(data => {
                // Populate the state dropdown
                for (const state in data) {
                    const option = document.createElement('option');
                    option.value = state;
                    option.textContent = state;
                    stateSelect.appendChild(option);
                }
            })
            .catch(error => console.error('Error fetching JSON:', error));

        // Handle state selection change
        stateSelect.addEventListener('change', function () {
            const selectedState = stateSelect.value;

            // Clear and reset city dropdown
            citySelect.innerHTML = '<option value="">Select City</option>';
            citySelect.disabled = true;

            if (selectedState) {
                fetch(jsonUrl)
                    .then(response => response.json())
                    .then(data => {
                        const cities = data[selectedState];
                        if (cities) {
                            cities.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city;
                                option.textContent = city;
                                citySelect.appendChild(option);
                            });
                            citySelect.disabled = false;
                        }
                    })
                    .catch(error => console.error('Error fetching JSON:', error));
            }
        });
    });
</script>
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all required elements
        const searchInput = document.getElementById('dealer-search-input');
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');
        const dealerList = document.getElementById('dealer-list');
        const dealerCount = document.getElementById('dealer-count');
        const dealerDetailsContainer = document.querySelector('.dealer-container');
        const jsonUrl = "/wp-content/themes/your-theme/assets/state-and-city.json"; // Update with your actual path

        // Store original dealer items for reset
        const originalDealerItems = Array.from(document.querySelectorAll('.dealer-item'));

        // Function to filter dealers based on all criteria
        function filterDealers() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedState = stateSelect.value;
            const selectedCity = citySelect.value;
            let visibleDealers = 0;

            const dealerItems = document.querySelectorAll('.dealer-item');
            dealerItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                const address = item.getAttribute('data-address').toLowerCase();
                const state = item.getAttribute('data-state');
                const city = item.getAttribute('data-city');
                const zipCode = item.getAttribute('data-zip');

                // Check all filter conditions
                const matchSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    address.includes(searchTerm) || 
                    zipCode.includes(searchTerm);
                    
                const matchState = !selectedState || state === selectedState;
                const matchCity = !selectedCity || city === selectedCity;

                // Show/hide based on all conditions
                if (matchSearch && matchState && matchCity) {
                    item.style.display = 'flex';
                    visibleDealers++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Update dealer count
            dealerCount.textContent = `Showing ${visibleDealers} dealerships`;

            // Select first visible dealer
            const firstVisibleDealer = document.querySelector('.dealer-item[style="display: flex"] input[type="radio"]');
            if (firstVisibleDealer) {
                firstVisibleDealer.checked = true;
                firstVisibleDealer.dispatchEvent(new Event('click'));
            }
        }

        // Initialize state dropdown
        fetch(jsonUrl)
            .then(response => response.json())
            .then(data => {
                // Add empty option first
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = 'Select State';
                stateSelect.appendChild(emptyOption);

                // Add state options
                Object.keys(data).forEach(state => {
                    const option = document.createElement('option');
                    option.value = state;
                    option.textContent = state;
                    stateSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading states:', error));

        // Handle state selection change
        stateSelect.addEventListener('change', function() {
            const selectedState = this.value;
            
            // Reset and disable city dropdown
            citySelect.innerHTML = '<option value="">Select City</option>';
            citySelect.disabled = true;

            if (selectedState) {
                // Load cities for selected state
                fetch(jsonUrl)
                    .then(response => response.json())
                    .then(data => {
                        const cities = data[selectedState];
                        if (cities && cities.length > 0) {
                            cities.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city;
                                option.textContent = city;
                                citySelect.appendChild(option);
                            });
                            citySelect.disabled = false;
                        }
                    })
                    .catch(error => console.error('Error loading cities:', error));
            }

            // Apply filters
            filterDealers();
        });

        // Event listeners for all filter inputs
        citySelect.addEventListener('change', filterDealers);
        searchInput.addEventListener('input', filterDealers);

        // Handle dealer selection
        function attachDealerRadioListeners() {
            const dealerRadios = document.querySelectorAll('input[name="bill"]');
            dealerRadios.forEach((radio, index) => {
                radio.addEventListener('click', function() {
                    const allDealerDetails = dealerDetailsContainer.querySelectorAll('.w-full.rounded-xl.bg-white');
                    allDealerDetails.forEach(detail => detail.classList.add('hidden'));
                    
                    const correspondingDetail = allDealerDetails[index];
                    if (correspondingDetail) {
                        correspondingDetail.classList.remove('hidden');
                    }
                });
            });
        }

        // Initial setup
        attachDealerRadioListeners();
        
        // Select first dealer by default
        const firstDealer = document.querySelector('input[name="bill"]');
        if (firstDealer) {
            firstDealer.checked = true;
            firstDealer.dispatchEvent(new Event('click'));
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Existing elements
        const tabs = document.querySelectorAll('.tab-item');
        const or = document.querySelector('.or');
        const searchSection = document.querySelector('.tab-item-view');
        const dealerList = document.getElementById('dealer-list');
        const dealerCount = document.getElementById('dealer-count');
        const dealerRadios = document.querySelectorAll('input[name="bill"]');
        const dealerDetailsContainer = document.querySelector('.dealer-container');
        const searchInput = document.getElementById('dealer-search-input');
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');

        // Store original dealer items for reset
        const originalDealerItems = Array.from(document.querySelectorAll('.dealer-item'));
        let userAgreedToLocationPermission = false;

        // Add click handlers to tabs
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');

                // Toggle search elements visibility
                if (this.textContent.trim().includes('Dealer near your location')) {
                    searchInput.style.display = 'none';
                    stateSelect.parentElement.parentElement.style.display = 'none';
                    or.style.display = 'none';
                    findNearestDealers();
                } else {
                    searchInput.style.display = 'block';
                    stateSelect.parentElement.parentElement.style.display = 'flex';
                    or.style.display = 'flex';
                    resetDealerList();
                }
            });
        });

        // Function to calculate distance between coordinates
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 3958.8; // Radius of the Earth in miles
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance in miles
        }

        // Function to reset dealer list to original state
        function resetDealerList() {
            dealerList.innerHTML = '';
            originalDealerItems.forEach(item => {
                const clonedItem = item.cloneNode(true);
                if (item.style.display !== 'none') {
                    clonedItem.classList.remove('hidden');
                }
                dealerList.appendChild(clonedItem);
            });

            // Reset dealer count
            dealerCount.textContent = 'Showing all dealerships';

            // Re-attach event listeners and select first dealer
            const newDealerRadios = document.querySelectorAll('#dealer-list .dealer-item input[type="radio"]');
            attachDealerRadioListeners(newDealerRadios);
            if (newDealerRadios.length > 0) {
                newDealerRadios[0].checked = true;
                newDealerRadios[0].dispatchEvent(new Event('click'));
            }
        }

        // Function to attach event listeners to dealer radio buttons
        function attachDealerRadioListeners(radios) {
            radios.forEach((radio, index) => {
                radio.addEventListener('click', function() {
                    const allDealerDetails = dealerDetailsContainer.querySelectorAll('.w-full.rounded-xl.bg-white');
                    allDealerDetails.forEach(detail => detail.classList.add('hidden'));

                    const correspondingDetail = allDealerDetails[index];
                    if (correspondingDetail) {
                        correspondingDetail.classList.remove('hidden');
                    }
                });
            });
        }

        // Function to find nearest dealers
        function findNearestDealers() {
            if (!navigator.geolocation) {
                dealerList.innerHTML = '<p class="text-center text-red-500">Geolocation is not supported by your browser</p>';
                return;
            }

            // Show loading state
            dealerList.innerHTML = '<p class="text-center">Finding dealers near you...</p>';
            dealerCount.textContent = 'Locating your position...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;

                    const dealersWithDistance = originalDealerItems.map(item => {
                        const dealerLat = parseFloat(item.getAttribute('data-latitude'));
                        const dealerLon = parseFloat(item.getAttribute('data-longitude'));

                        if (isNaN(dealerLat) || isNaN(dealerLon)) {
                            return null;
                        }

                        const distance = calculateDistance(userLat, userLon, dealerLat, dealerLon);
                        return {
                            element: item.cloneNode(true),
                            distance: distance
                        };
                    }).filter(dealer => dealer !== null);

                    // Sort dealers by distance
                    dealersWithDistance.sort((a, b) => a.distance - b.distance);

                    // Clear and repopulate dealer list
                    dealerList.innerHTML = '';
                    dealersWithDistance.forEach(dealer => {
                        const dealerItemElement = document.createElement('label');
                        dealerItemElement.className = 'dealer-item cursor-pointer py-4 flex gap-4 items-center border-b border-solid border-neutral-200';

                        // Copy data attributes
                        Array.from(dealer.element.attributes).forEach(attr => {
                            if (attr.name.startsWith('data-')) {
                                dealerItemElement.setAttribute(attr.name, attr.value);
                            }
                        });

                        dealerItemElement.setAttribute('data-distance', dealer.distance.toFixed(1));

                        dealerItemElement.innerHTML = `
                            <input type="radio" name="bill" class="radio-blue">
                            <div class="flex flex-col gap-1">
                                <h2 class="text-body-md-mdeium text-gray-8">${dealer.element.querySelector('h2').textContent}</h2>
                                <p class="text-body-md-regular text-neutral-500">
                                    ${dealer.element.querySelector('p').textContent}
                                </p>
                                <span class="text-body-sm-regular text-neutral-500">${dealer.distance.toFixed(1)} miles</span>
                            </div>
                        `;

                        dealerList.appendChild(dealerItemElement);
                    });

                    // Update dealer count
                    dealerCount.textContent = `Showing ${dealersWithDistance.length} dealers near you`;

                    // Attach listeners to new radio buttons
                    const newDealerRadios = document.querySelectorAll('#dealer-list .dealer-item input[type="radio"]');
                    attachDealerRadioListeners(newDealerRadios);

                    // Select first dealer
                    if (newDealerRadios.length > 0) {
                        newDealerRadios[0].checked = true;
                        newDealerRadios[0].dispatchEvent(new Event('click'));
                    }
                },
                function(error) {
                    let errorMessage = 'Unknown error';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'You denied location access. Please enable permissions in your browser settings.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'The request to get user location timed out.';
                            break;
                    }
                    dealerList.innerHTML = `<p class="text-center text-red-500">${errorMessage}</p>`;
                    dealerCount.textContent = 'Could not find your location';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        }

        // Initial event listeners for dealer radios
        attachDealerRadioListeners(dealerRadios);

    // Ensure first dealer is selected by default
    if (dealerRadios.length > 0) {
        dealerRadios[0].checked = true;
        dealerRadios[0].dispatchEvent(new Event('click'));
    }
});

    document.addEventListener('DOMContentLoaded', function() {
        const viewMoreBtn = document.getElementById('view-more');
        const dealerItems = document.querySelectorAll('.dealer-item');
        const viewMoreIcon = document.querySelector('.view-more-icon img');
        const viewMoreText = document.querySelector('.view-more-text');
        const hiddenItems = Array.from(dealerItems).slice(6);

        if (viewMoreBtn) {
            let isExpanded = false;

            viewMoreBtn.addEventListener('click', function() {
                if (!isExpanded) {
                    // Expand: Show all items
                    hiddenItems.forEach(item => {
                        item.classList.remove('hidden');
                    });

                    // Rotate icon and change text
                    viewMoreIcon.classList.add('rotate-180');
                    viewMoreText.textContent = 'View less';
                    isExpanded = true;
                } else {
                    // Collapse: Hide items after 6th
                    hiddenItems.forEach(item => {
                        item.classList.add('hidden');
                    });

                    // Rotate icon back and change text
                    viewMoreIcon.classList.remove('rotate-180');
                    viewMoreText.textContent = 'View more detail';
                    isExpanded = false;
                }
            });
        }
    });
</script>