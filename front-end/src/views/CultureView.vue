<template>
  <section class="articles">
    <article>
      <div class="article-wrapper">
        <figure>
          <img :src="articleImage" alt="" />
        </figure>
        <div class="article-body">
          <h2>{{ articleTitle }}</h2>
          <p>{{ articleDescription }}</p>
          <a href="#" class="read-more">
            Read more <span class="sr-only">about this is some title</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0010-1.414z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
      </div>
    </article>

    <article>
      <div class="article-wrapper">
        <figure>
          <img :src="base64Image" alt="Base64 Image" />
        </figure>
        <div class="article-body">
          <h2>{{ articleTitle2 }}</h2>
          <p>{{ articleDescription2 }}</p>
          <a href="#" class="read-more">
            Read more <span class="sr-only">about another article</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0010-1.414z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
      </div>
    </article>
  </section>
</template>

<script>
export default {
  data() {
    return {
      articleImage: "https://picsum.photos/id/1011/800/450",
      articleTitle: "Vanšu tilts",
      articleDescription: "A brief description of the Vanšu tilts bridge.",
      base64Image: "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAsJCQcJCQcJCQkJCwkJCQkJCQsJCwsMCwsLDA0QDBEODQ4MEhkSJRodJR0ZHxwpKRYlNzU2GioyPi0pMBk7IRP/2wBDAQcICAsJCxULCxUsHRkdLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCz/wAARCAGRAQYDASIAAhEBAxEB",
      articleTitle2: "Another Article Title",
      articleDescription2: "Description of the second article here.",
    };
  },
};
</script>

<style scoped>
/* Add your styles here */
.articles {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.article-wrapper {
  border: 1px solid #ccc;
  border-radius: 8px;
  overflow: hidden;
  width: 100%;
  max-width: 800px;
}

.article-body {
  padding: 20px;
}

h2 {
  margin-top: 0;
}

.read-more {
  display: inline-flex;
  align-items: center;
  color: #007bff;
  text-decoration: none;
}

.read-more svg {
  margin-left: 5px;
  width: 16px;
  height: 16px;
}

.icon {
  width: 20px;
  height: 20px;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  border: 0;
  clip: rect(0, 0, 0, 0);
  clip-path: inset(50%);
  overflow: hidden;
}
</style>
