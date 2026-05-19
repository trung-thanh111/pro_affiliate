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
                    
                    // Open the detailed article in a new tab
                    window.open(articleUrl, '_blank');
                    
                    // Redirect the current tab to the affiliate link
                    window.location.href = redirectUrl;
                }
            }
        });
    }

    // 2. Handle first user interaction inside the post detail page
    function initDetailRedirect() {
        const artDetail = document.getElementById('art-detail');
        if (!artDetail) return;

        const affiliateUrl = artDetail.getAttribute('data-redirect');
        const postId = artDetail.getAttribute('data-post-id');

        if (affiliateUrl && postId) {
            const sessionKey = 'affiliate_redirected_' + postId;

            // Check if already redirected in this session
            if (!sessionStorage.getItem(sessionKey)) {
                const handleRedirect = function (e) {
                    // Ignore clicks on sharing buttons and copy link button
                    if (e && e.target && (e.target.closest('.share-icon') || e.target.closest('.btn-copy-link-article'))) {
                        return;
                    }
                    
                    // Mark session as redirected
                    sessionStorage.setItem(sessionKey, 'true');

                    // Remove event listeners immediately
                    document.removeEventListener('click', handleRedirect);
                    document.removeEventListener('touchstart', handleRedirect);

                    // Open affiliate link in a new focused tab
                    window.open(affiliateUrl, '_blank');
                };

                // Add event listeners for direct user interaction
                document.addEventListener('click', handleRedirect);
                document.addEventListener('touchstart', handleRedirect);
            }
        }
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
