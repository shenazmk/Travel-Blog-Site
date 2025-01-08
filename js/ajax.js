// Initialize a variable to store the last blog ID
var lastBlogId = 0;  // Default is 0, meaning no blogs are loaded yet

// Function to initiate the AJAX request and process the result
function myTimer(numPosts) {
    // Get the most recent blog ID (last blog displayed on the page)
    var currentLastBlogId = lastBlogId;

    // Create a new XMLHttpRequest to fetch new blog posts
    var xhr = new XMLHttpRequest();

    // Define what happens when the response is received
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Parse the response JSON data
            var data = JSON.parse(xhr.responseText);

            // Check if there are new blog posts to display
            if (data.blogs && data.blogs.length > 0) {
                // For each new blog post, create the HTML content and prepend to the page
                data.blogs.forEach(function(post) {
                    var postHtml = `
                        <div class="post" id="post-${post.blog_id}">
                            <div class="post-header">
                                <div class="avatar">
                                    <img src="images/${post.avatar}" alt="Avatar">
                                </div>
                                <div class="screenname">${post.screenname}</div>
                                <div class="date-time">${new Date(post.timestamp).toLocaleString()}</div>
                            </div>
                            <div class="post-details">
                                <div class="post-content">
                                    <img class="post-images" src="images/${post.image}" alt="Post Image">
                                    <h3>${post.title}</h3>
                                    <p>${post.content.length > 150 ? post.content.substring(0, 150) + "..." : post.content}</p>
                                    <div class="white-font">
                                        <p><i class="fa-solid fa-message"></i> ${post.comment_count}</p>
                                    </div>
                                    <a href="blogpost.php?id=${post.blog_id}">See more</a>
                                </div>
                            </div>
                        </div>
                    `;

                    // Prepend the new blog post to the top of the posts section
                    document.getElementById("posts-section").insertAdjacentHTML('afterbegin', postHtml);
                });

                // Update the lastBlogId to the ID of the latest blog post
                lastBlogId = data.blogs[0].blog_id;

                // Ensure the total number of posts does not exceed the limit (5 or 20)
                removeOldPosts(numPosts);
            }
        }
    };

    // Prepare the GET request, sending `last_blog_id` and `num_posts` as query parameters
    xhr.open('GET', `ajax.php?last_blog_id=${currentLastBlogId}&num_posts=${numPosts}`, true);

    // Send the request
    xhr.send();
}

// Function to remove old posts if the number of posts exceeds the limit
function removeOldPosts(maxPosts) {
    var postsSection = document.getElementById("posts-section");
    var allPosts = postsSection.querySelectorAll(".post");

    // Remove posts until we are at the correct number of posts
    while (allPosts.length > maxPosts) {
        postsSection.removeChild(allPosts[allPosts.length - 1]);
    }
}

// Call myTimer every 2 minutes (120000 ms) to check for new posts
setInterval(function() {
    var numPosts = isLoggedIn ? 20 : 5;  // Adjust number of posts based on login status
    myTimer(numPosts);  // Call the function to check for new posts
}, 120000);  

function isLoggedIn() {
    return isLoggedIn;  // This checks the global `isLoggedIn` variable passed from PHP
}
