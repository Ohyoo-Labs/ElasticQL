const query = {
  schema: 'users',
  fields: ['id', 'name', 'email'],
  rels: {
      posts: {
          fields: ['id', 'title']
      }
  },
  conditions: {
      id: 1
  }
};

fetch('http://localhost/api.php', {
  method: 'POST',
  headers: {
      'Content-Type': 'application/json'
  },
  body: JSON.stringify(query)
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));