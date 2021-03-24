CREATE TABLE `method_demo` (
  `id_step_demo` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
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
  `id_method_list` int(10)
);

CREATE TABLE `method_list` (
  `id_method_list` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `method_name` varchar(50),
  `creation_date` datetime DEFAULT (now())
);

CREATE TABLE `waiting_condition` (
  `id_waiting_condition` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `waiting_label` varchar(50)
);

CREATE TABLE `method_demo_waiting` (
  `id_method_waiting_condition` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `timeout_value` int(10),
  `waiting_value_label` varchar(100),
  `id_waiting_condition` int(10),
  `id_step_demo` int(10)
);

CREATE TABLE `signal_type` (
  `id_signal_type` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `signal_type` varchar(50),
  `unity` varchar(10)
);

CREATE TABLE demo_threshold (
  `id_threshold` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `threshold_value` int(10),
  `id_operation` int(10),
  `id_signal_type` int(10),
  `id_method_waiting_condition` int(10)
);

CREATE TABLE `operation` (
  `id_operation` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `operation` varchar(50)
);

CREATE TABLE `measures_values` (
  `id_m_value` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `start_date` datetime,
  `end_time` datetime,
  `measure_value` float,
  `id_step_demo` int(10)
);

ALTER TABLE `method_demo` ADD FOREIGN KEY (`id_method_list`) REFERENCES `method_list` (`id_method_list`);

ALTER TABLE demo_threshold ADD FOREIGN KEY (`id_operation`) REFERENCES `operation` (`id_operation`);

ALTER TABLE demo_threshold ADD FOREIGN KEY (`id_signal_type`) REFERENCES `signal_type` (`id_signal_type`);

ALTER TABLE `method_demo_waiting` ADD FOREIGN KEY (`id_step_demo`) REFERENCES `method_demo` (`id_step_demo`);

ALTER TABLE `method_demo_waiting` ADD FOREIGN KEY (`id_waiting_condition`) REFERENCES `waiting_condition` (`id_waiting_condition`);

ALTER TABLE `measures_values` ADD FOREIGN KEY (`id_step_demo`) REFERENCES `method_demo` (`id_step_demo`);

ALTER TABLE demo_threshold ADD FOREIGN KEY (`id_method_waiting_condition`) REFERENCES `method_demo_waiting` (`id_method_waiting_condition`);
