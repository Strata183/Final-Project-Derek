
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(255),
    Email VARCHAR(100) NOT NULL,
    Admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE Categories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(100) NOT NULL
);

CREATE TABLE Tags (
    TagID INT AUTO_INCREMENT PRIMARY KEY,
    TagName VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE Posts (
    PostID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    CategoryID INT,
    Title VARCHAR(255) NOT NULL,
    Content TEXT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID)
);

CREATE TABLE Post_Tags (
    PostID INT,
    TagID INT,
    PRIMARY KEY (PostID, TagID),
    FOREIGN KEY (PostID) REFERENCES Posts(PostID) ON DELETE CASCADE,
    FOREIGN KEY (TagID) REFERENCES Tags(TagID) ON DELETE CASCADE
);

CREATE TABLE Comments (
    CommentID INT AUTO_INCREMENT PRIMARY KEY,
    PostID INT,
    UserID INT,
    Comment TEXT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (PostID) REFERENCES Posts(PostID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);


CREATE TABLE RawData (
    UserID INT, Username VARCHAR(50), Password VARCHAR(255), Email VARCHAR(100), Admin BOOLEAN,
    PostID INT, Title VARCHAR(255), Content TEXT,
    CategoryID INT, CategoryName VARCHAR(100),
    CommentID INT, Comment TEXT,
    TagID INT, TagName VARCHAR(50)
);



INSERT INTO Users (UserID, Username, Password, Email, Admin) 
    SELECT UserID, MAX(Username), MAX(Password), MAX(Email), MAX(Admin) 
    FROM RawData WHERE UserID IS NOT NULL GROUP BY UserID;

INSERT INTO Categories (CategoryID, CategoryName) 
    SELECT CategoryID, MAX(CategoryName) 
    FROM RawData WHERE CategoryID IS NOT NULL GROUP BY CategoryID;

INSERT INTO Tags (TagID, TagName) 
    SELECT TagID, MAX(TagName) 
    FROM RawData WHERE TagID IS NOT NULL GROUP BY TagID;

INSERT INTO Posts (PostID, UserID, CategoryID, Title, Content) 
    SELECT PostID, MAX(UserID), MAX(CategoryID), MAX(Title), MAX(Content) 
    FROM RawData WHERE PostID IS NOT NULL GROUP BY PostID;

INSERT INTO Comments (CommentID, PostID, UserID, Comment) 
    SELECT CommentID, MAX(PostID), MAX(UserID), MAX(Comment) 
    FROM RawData WHERE CommentID IS NOT NULL GROUP BY CommentID;

INSERT INTO Post_Tags (PostID, TagID) 
    SELECT DISTINCT PostID, TagID 
    FROM RawData WHERE TagID IS NOT NULL AND PostID IS NOT NULL;