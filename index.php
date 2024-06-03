<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2a3439; /* Gunmetal blue */
        }
        .container {
            margin-top: 50px;
        }
        .box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 box">
                <h1 class="text-center">URL Shortener</h1>
                <form action="shorten.php" method="post" id="urlForm">
                    <div class="form-group">
                        <label for="url">Enter URL:</label>
                        <input type="url" id="url" name="url" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Shorten</button>
                </form>
                <div class="mt-4">
                    <a href="#" id="historyLink" data-toggle="modal" data-target="#historyModal">View History</a>
                </div>
                <div id="shortUrl" class="mt-4"></div>
                <div class="mt-4">
                    <p>Your Token: <span id="userToken"></span></p>
                    <a href="#" data-toggle="modal" data-target="#restoreModal">Restore Token</a>
                </div>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">URL Shortening History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="historyList" class="list-group">
                        <!-- History items will be appended here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Token Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreModalLabel">Restore Token</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="restoreTokenForm">
                        <div class="form-group">
                            <label for="restoreToken">Enter Token:</label>
                            <input type="text" id="restoreToken" name="restoreToken" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Restore</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let userToken = localStorage.getItem('userToken');
            if (!userToken) {
                userToken = generateToken();
                localStorage.setItem('userToken', userToken);
            }
            document.getElementById('userToken').textContent = userToken;

            document.getElementById('urlForm').onsubmit = async function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                formData.append('token', userToken);
                const response = await fetch('shorten.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.text();
                document.getElementById('shortUrl').innerHTML = result;
            };

            document.getElementById('historyLink').onclick = async function() {
                const response = await fetch('history.php?token=' + userToken);
                const history = await response.json();
                const historyList = document.getElementById('historyList');
                historyList.innerHTML = '';

                history.forEach(entry => {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item';
                    listItem.innerHTML = `<strong>Short URL:</strong> <a href="${entry.shortUrl}">${entry.shortUrl}</a><br><strong>Original URL:</strong> ${entry.originalUrl}`;
                    historyList.appendChild(listItem);
                });
            };

            document.getElementById('restoreTokenForm').onsubmit = function(event) {
                event.preventDefault();
                const newToken = document.getElementById('restoreToken').value;
                localStorage.setItem('userToken', newToken);
                document.getElementById('userToken').textContent = newToken;
                $('#restoreModal').modal('hide');
            };
        });

        function generateToken() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }
    </script>
</body>
</html>
