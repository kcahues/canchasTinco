CREATE DATABASE Canchas_Tinco;

USE Canchas_Tinco;

CREATE TABLE Rol(
    idRol INT,
    descripcion VARCHAR(45) NOT NULL,
    PRIMARY KEY (idRol)
);

CREATE TABLE Usuario(
    idUsuario INT,
    idRol INT NOT NULL,
    nombre VARCHAR (50) NOT NULL,
    apellido VARCHAR (50) NOT NULL,
    correoElectronico VARCHAR (100) NOT NULL,
    telefono INT NOT NULL,
    contrasenia VARCHAR (500) NOT NULL,
    PRIMARY KEY (idUsuario),
    CONSTRAINT fk_idRol FOREIGN KEY (idRol)
    REFERENCES Rol(idRol) ON DELETE RESTRICT
);

CREATE TABLE TipoAnticipo(
    idTipoAnticipo INT,
    descripcion VARCHAR(45) NOT NULL,
    PRIMARY KEY (idTipoAnticipo)
);

CREATE TABLE Anticipo(
    idAnticipo INT,
    montoAnticipo DECIMAL (13,2) NOT NULL,
    fechaAnticipo DATETIME NOT NULL,
    tipoAnticipo INT NOT NULL,
    noDocumento VARCHAR(40),
    motivo VARCHAR (50),
    PRIMARY KEY (idAnticipo),
    CONSTRAINT fk_idTipoAnticipo FOREIGN KEY (tipoAnticipo)
    REFERENCES TipoAnticipo(idTipoAnticipo)
);

CREATE TABLE Cliente(
    idCliente INT,
    nombre VARCHAR (50) NOT NULL,
    apellido VARCHAR (50) NOT NULL,
    telefono  INT NOT NULL,
    telefono2 INT NOT NULL,
    PRIMARY KEY (idCliente)
);

CREATE TABLE EstadoReserva(
    idEstadoReserva INT,
    descripcion VARCHAR(45) NOT NULL,
    PRIMARY KEY (idEstadoReserva)
);

CREATE TABLE TipoCancha(
    idTipoCancha INT,
    descripcion VARCHAR(45) NOT NULL,
    PRIMARY KEY (idTipoCancha)
);

CREATE TABLE Cancha(
    idCancha INT,
    idTipoCancha INT NOT NULL,
    nombre VARCHAR(45) NOT NULL,
    descripcion VARCHAR(200) NOT NULL,
    PRIMARY KEY (idCancha),
    CONSTRAINT fk_idTipoCancha FOREIGN KEY (idTipoCancha)
    REFERENCES TipoCancha(idTipoCancha)
);

CREATE TABLE Tarifa(
    idTarifa INT,
    horaIni TIME NOT NULL,
    horaFin TIME NOT NULL,
    precio DECIMAL(13,2) NOT NULL,
    descripcion VARCHAR(100) NOT NULL,
    PRIMARY KEY (idTarifa)
);

CREATE TABLE Horario(
    idHorario INT,
    idCancha INT NOT NULL,
    idTarifa INT NOT NULL,
    PRIMARY KEY (idCancha),
    CONSTRAINT fk_idCancha FOREIGN KEY (idCancha)
    REFERENCES Cancha(idCancha),
    CONSTRAINT fk_idTarifa FOREIGN KEY (idTarifa)
    REFERENCES Tarifa(idTarifa)
);

CREATE TABLE Reserva(
    idReserva INT,
    idUsuario INT NOT NULL,
    idHorario INT NOT NULL,
    idAnticipo INT NOT NULL,
    idCliente INT NOT NULL,
    idEstadoReserva INT NOT NULL,
    fechaReserva DATETIME,
    fechaConfirmacion DATETIME,
    PRIMARY KEY (idReserva),
    CONSTRAINT fk_idUsuario FOREIGN KEY (idUsuario)
    REFERENCES Usuario(idUsuario),
    CONSTRAINT fk_idHorario FOREIGN KEY (idHorario)
    REFERENCES Horario(idTarifa),
    CONSTRAINT fk_idAnticipo FOREIGN KEY (idAnticipo)
    REFERENCES Anticipo(idAnticipo),
    CONSTRAINT fk_idCliente FOREIGN KEY (idCliente)
    REFERENCES Cliente(idCliente),
    CONSTRAINT fk_idEstadoReserva FOREIGN KEY (idEstadoReserva)
    REFERENCES EstadoReserva(idEstadoReserva)
);