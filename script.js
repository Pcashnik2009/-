document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector("#comment-form");

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {
            name: formData.get('name'),
            comment: formData.get('comment')
        };
        
        await fetch('add_comment.php', {
            method: 'POST',
            body: JSON.stringify(data),
        });

        form.reset();

        loadComments();
    });

    async function loadComments(){
        const res = await fetch('load_comments.php'); // эту точку нужно сделать
        const comments = await res.json();

        const ul = document.querySelector("#comments-list");
        ul.innerHTML = '';
        comments.forEach(el => {
            ul.innerHTML += `<li><strong>${el.name}:</strong> ${el.comment} <em>(${el.created_at})</em></li>`;
        })
    }
  
    loadComments();

});
