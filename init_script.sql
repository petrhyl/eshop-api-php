DROP TABLE products;

CREATE TABLE products(
    id_product INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    price DOUBLE(11,2) DEFAULT 0,
    description VARCHAR(500),
    PRIMARY KEY (id_product)
);

DROP TABLE pictures;

CREATE TABLE pictures(
    id_picture INT NOT NULL AUTO_INCREMENT,
    file_name VARCHAR(255) NOT NULL,
    id_product INT NOT NULL,
    PRIMARY KEY (id_picture)
);

DROP TABLE customers;

CREATE TABLE customers(
    id_customer INT NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    phone INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    PRIMARY KEY (id_customer),
    UNIQUE (email)
);

DROP TABLE addresses;

CREATE TABLE addresses(
    id_address INT NOT NULL AUTO_INCREMENT,
    street VARCHAR(100) NOT NULL,
    house_number VARCHAR(20) NOT NULL,
    town VARCHAR(100) NOT NULL,
    postal_code INt NOT NULL,
    country VARCHAR(80) NOT NULL,
    id_customer INT NOT NULL,
    PRIMARY KEY (id_address)
);

DROP TABLE orders;

CREATE TABLE orders(
    id_order BIGINT NOT NULL AUTO_INCREMENT,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    id_customer INT NOT NULL,
    PRIMARY KEY (id_order)
);

ALTER TABLE orders AUTO_INCREMENT=100000;

DROP TABLE ordered_products;

CREATE TABLE ordered_products(
    id_ord_product INT NOT NULL AUTO_INCREMENT,
    ordered_price DOUBLE(11,2) NOT NULL,
    quantity SMALLINT NOT NULL DEFAULT 1,
    id_product INT NOT NULL,
    id_order BIGINT NOT NULL,
    PRIMARY KEY (id_ord_product)
);


ALTER TABLE pictures
ADD FOREIGN KEY (id_product) REFERENCES products(id_product);

ALTER TABLE addresses
ADD FOREIGN KEY (id_customer) REFERENCES customers(id_customer);

ALTER TABLE orders
ADD FOREIGN KEY (id_customer) REFERENCES customers(id_customer);

ALTER TABLE ordered_products
ADD FOREIGN KEY (id_order) REFERENCES orders(id_order);

ALTER TABLE ordered_products
ADD FOREIGN KEY (id_product) REFERENCES products(id_product);