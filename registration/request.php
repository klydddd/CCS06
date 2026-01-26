<html>
<head>
    <title>Request Page</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        h1 {
            color: #333;
            text-align: center;
            padding-bottom: 20px;
        }

        form {
            margin-top: 20px;
            background: white;
            height: 50%;
            padding: 40px;
            border-radius: 40px;
            box-shadow: 10px 10px 30px rgba(0, 0, 0, 0.1);
            /* display: flex;
            flex-direction: column;
            align-items: center;
            width: ; */
        }

        label {
            display: inline-block;
            width: 100px;
            margin-bottom: 10px;
        }

        input[type="text"], select {
            width: 80%;
            height: 40px;
            border: solid 1px #ccc;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        input[type="radio"], input[type="checkbox"] {
            padding: 10px;
            background-color: red;
        }

        button {
            width: 100%;
            height: 40px;
            border: solid 1px #282aa7;
            border-radius: 10px;
            padding: 10px 15px;
            background-color: #3128a7;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #2b2188;
        }
    </style>
</head>

<body>
    
    
    <form methods="GET" action="response.php">
        <h1>Fill in the form</h1>

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name">
        <br>
        <br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name">
        <br>   
        <br>
        <!-- <input type="submit" name="submit" value="SEND" target="_blank"> -->
        
        <label for="country">Country:</label>
        <select class="form-select" autocomplete="country" id="country" name="country">
            <option>Select Country</option>
            <option value="USA">United States</option>
            <option value="Canada">Canada</option>
            <option value="Mexico">Mexico</option>
            <option value="Brazil">Brazil</option>
            <option value="Argentina">Argentina</option>
        </select>
        

        <br><br>

        <label for="gender">Gender:</label>
        <input type="radio" id="male" name="gender" value="male"><label for="male">Male</label>
        <input type="radio" id="female" name="gender" value="female"><label for="female">Female</label>
        <input type="radio" id="other" name="gender" value="other"><label for="other">Other</label>

        <br><br>

        <label for="">Colors:</label>
        <input type="checkbox" id="red" name="color[]" value="red"><label for="red">red</label>
        <input type="checkbox" id="blue" name="color[]" value="blue"><label for="blue">blue</label>
        <input type="checkbox" id="green" name="color[]" value="green"><label for="green">green</label>
        <input type="checkbox" id="yellow" name="color[]" value="yellow"><label for="yellow">yellow</label>
        <input type="checkbox" id="purple" name="color[]" value="purple"><label for="purple">purple</label>
        <br><br>
        <button type="submit">SEND</button>

        
    </form>


</body>
</html>