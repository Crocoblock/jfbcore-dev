<?php


namespace DevCoreJFB;


class RenameNamespace extends BaseScript {

	public function slug(): string {
		return 'replace-namespace';
	}

	public function run(): void {
		if (
			empty( $this->config['search'] )
			|| empty( $this->config['replace'] )
			|| $this->config['search'] === $this->config['replace']
		) {
			return;
		}

		$rootPath = dirname( __DIR__, 4 );

		$this->readPath( $rootPath );
	}

	public function readPath( $rootPath ) {
		foreach ( scandir( $rootPath ) as $element ) {
			if ( in_array( $element, array( '.', '..' ) ) ) {
				continue;
			}

			$currentPath = $rootPath . DIRECTORY_SEPARATOR . $element;

			if ( is_dir( $currentPath ) ) {
				$this->readPath( $currentPath );
			} elseif ( is_file( $currentPath ) && preg_match( '/^.+\.(php|lock)$/i', $element ) ) {
				$currentFile = file_get_contents( $currentPath );

				if ( false !== strripos( $currentFile, $this->config['replace'] ) ) {
					echo "Replace '{$this->config['replace']}' is already in the file:\r\n$currentPath\r\n\n";
					continue;
				}

				$updatedCurrentFile = str_replace( $this->config['search'], $this->config['replace'], $currentFile );

				if ( $currentFile === $updatedCurrentFile ) {
					echo "Search '{$this->config['search']}' not founded in:\r\n$currentPath\r\n\n";
					continue;
				}

				$result = file_put_contents( $currentPath, $updatedCurrentFile );

				echo $result === false
					? "Error adding to file:\r\n$currentPath\r\n\n"
					: "Successfully put into file:\r\n$currentPath\r\n\n";
			}
		}
	}
}