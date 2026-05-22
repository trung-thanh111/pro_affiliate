(function () {
    // 1. Handle clicking post cards on homepage/listing pages
    function initHomepageRedirects() {
        document.addEventListener('click', function (e) {
            const postLink = e.target.closest('.post-link-redirect');
            if (postLink) {
                const redirectUrl = postLink.getAttribute('data-redirect') || postLink.closest('[data-redirect]')?.getAttribute('data-redirect');
                if (redirectUrl) {
                    e.preventDefault();
                    const articleUrl = postLink.getAttribute('href');
                    
                    // Open the affiliate link in a new tab (focus active)
                    window.open(redirectUrl, '_blank');
                    
                    // Redirect the current tab to the detailed article
                    window.location.href = articleUrl;
                }
            }
        });
    }

    // 2. Handle redirects inside the post detail page with cooldown and limit
    function initDetailRedirect() {
        const artDetail = document.getElementById('art-detail');
        if (!artDetail) return;

        const affiliateUrl = artDetail.getAttribute('data-redirect');
        const postId = artDetail.getAttribute('data-post-id');
        let randomLinks = [];
        try {
            randomLinks = JSON.parse(artDetail.getAttribute('data-random-redirects') || '[]');
        } catch (e) {
            console.error('Failed to parse random redirects', e);
        }

        if (!postId) return;

        const countKey = 'affiliate_redirect_count_' + postId;
        const timeKey = 'affiliate_last_redirect_time_' + postId;
        const maxRedirects = 5; // Max 5 redirects per post session (first + 4 randoms)

        const handleInteraction = function () {
            const now = Date.now();
            const currentCount = parseInt(sessionStorage.getItem(countKey) || '0', 10);

            // Stop if maximum redirect count reached
            if (currentCount >= maxRedirects) {
                return;
            }

            // Flow 1: First redirect
            if (currentCount === 0) {
                if (affiliateUrl) {
                    sessionStorage.setItem(countKey, '1');
                    sessionStorage.setItem(timeKey, now.toString());

                    // Open main affiliate link in a new tab (active focus)
                    window.open(affiliateUrl, '_blank');
                }
                return;
            }

            // Flow 2: Subsequent redirects (after 3 minutes from the last redirect)
            const lastRedirectTime = parseInt(sessionStorage.getItem(timeKey) || '0', 10);
            if (lastRedirectTime > 0 && (now - lastRedirectTime >= 180000)) { // 3 minutes = 180,000 ms
                if (randomLinks.length > 0) {
                    const nextCount = currentCount + 1;
                    sessionStorage.setItem(countKey, nextCount.toString());
                    sessionStorage.setItem(timeKey, now.toString());

                    // Pick a random affiliate link from the pool
                    const randomIndex = Math.floor(Math.random() * randomLinks.length);
                    const randomUrl = randomLinks[randomIndex];

                    if (randomUrl) {
                        // Open random affiliate link in a new tab (active focus)
                        window.open(randomUrl, '_blank');
                    }
                }
            }
        };

        // Add event listeners for direct user interaction anywhere on the body
        document.addEventListener('click', handleInteraction);
        document.addEventListener('touchstart', handleInteraction);
    }

    function init() {
        initHomepageRedirects();
        initDetailRedirect();
    }

    // Run immediately if DOM is already parsed/interactive, otherwise wait for DOMContentLoaded
    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        init();
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }
})();
