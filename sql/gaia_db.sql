CREATE TABLE `method_demo` (
  `id_method` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `step` int(10),
  `A1` int(10),
  `A2` int(10),
  `A3` int(10),
  `A4` int(10),
  `A5` int(10),
  `B1` int(10),
  `B2` int(10),
  `B3` int(10),
  `B4` int(10),
  `B5` int(10),
  `C1` int(10),
  `C2` int(10),
  `C3` int(10),
  `C4` int(10),
  `C5` int(10),
  `pump` int(10),
  `oven` int(10),
  `lifter` float,
  `description` varchar(50),
  `id_waiting_condition` int(10),
  `id_measure_type` int(10),
  `id_method_name` int(10)
);

CREATE TABLE `method_name` (
  `id_method_name` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `method_name` varchar(50),
  `creation_date` datetime
);

CREATE TABLE `waiting_condition` (
  `id_waiting_condition` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `waiting_condition` varchar(50)
);

CREATE TABLE `measure_type` (
  `id_measure_type` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `measure_type` varchar(50),
  `unity` varchar(10)
);

CREATE TABLE `ref_value` (
  `id_ref_value` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `ref_value` int(10)
);

CREATE TABLE `error_margin` (
  `id_error_margin` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `operation` varchar(100),
  `error_margin` int(10)
);

CREATE TABLE `measures_values` (
  `id_m_value` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `start_date` datetime,
  `end_time` datetime,
  `measure_value` float,
  `id_method` int(10),
  `id_ref_value` int(10),
  `id_error_margin` int(10)
);

CREATE TABLE `waiting_values` (
  `id_waiting_value` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `waiting_value` float,
  `id_waiting_condition` int(10),
  `id_method` int(10)
);

ALTER TABLE `measures_values` ADD FOREIGN KEY (`id_method`) REFERENCES `method_demo` (`id_method`);

ALTER TABLE `method_demo` ADD FOREIGN KEY (`id_method_name`) REFERENCES `method_name` (`id_method_name`);

ALTER TABLE `measures_values` ADD FOREIGN KEY (`id_ref_value`) REFERENCES `ref_value` (`id_ref_value`);

ALTER TABLE `method_demo` ADD FOREIGN KEY (`id_measure_type`) REFERENCES `measure_type` (`id_measure_type`);

ALTER TABLE `measures_values` ADD FOREIGN KEY (`id_error_margin`) REFERENCES `error_margin` (`id_error_margin`);

ALTER TABLE `waiting_values` ADD FOREIGN KEY (`id_method`) REFERENCES `method_demo` (`id_method`);

ALTER TABLE `waiting_values` ADD FOREIGN KEY (`id_waiting_condition`) REFERENCES `waiting_condition` (`id_waiting_condition`);
