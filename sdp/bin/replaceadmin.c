#include <stdio.h>
#include <stdlib.h>
#include <libfprint/fprint.h>

void updategui(const char *data) {
	FILE *file = fopen("../gui/data.txt","w");
        fprintf(file,data);
        fclose(file);
        system("sudo -u pi DISPLAY=:0 xdotool key F5");
}

int main() {
	int tmp = fp_init();
	if(tmp != 0) {
		fprintf(stderr, "fprintd init error, error code: %d\n", tmp);
		return -1;
	}

	struct fp_dscv_dev **devs = fp_discover_devs();
	struct fp_dscv_dev *dscv_dev = devs[0];
	struct fp_dev *dev = fp_dev_open(dscv_dev);

	struct fp_print_data *enrolled_print = NULL;
	int r;
	int stage = 1;
	char str[256];

	updategui("enroll.php|1|");

	do {
		printf("\nScan your finger now.\n");

		r = fp_enroll_finger(dev, &enrolled_print);
		if (r < 0) {
			printf("Enroll failed with error %d\n", r);
			break;
		}

		switch (r) {
			case FP_ENROLL_COMPLETE:
				printf("Enroll complete!\n");
				updategui("enrollcomplete.php|admin");
				break;
			case FP_ENROLL_FAIL:
				printf("Enroll failed, something wen't wrong :(\n");
				snprintf(str, sizeof(str), "enroll.php|%d|Something went wrong while scanning", stage);
				updategui(str);
				break;
			case FP_ENROLL_PASS:
				printf("Enroll stage passed. Yay!\n");
				stage++;
				snprintf(str, sizeof(str), "enroll.php|%d|", stage);
				updategui(str);
				break;
			case FP_ENROLL_RETRY:
				printf("Didn't quite catch that. Please try again.\n");
				snprintf(str, sizeof(str), "enroll.php|%d|Fingerprint unclear, please try again", stage);
                                updategui(str);
				break;
			case FP_ENROLL_RETRY_TOO_SHORT:
				printf("Your swipe was too short, please try again.\n");
				snprintf(str, sizeof(str), "enroll.php|%d|Finger lifted before fingerprint can be read, please try again", stage);
                                updategui(str);
				break;
			case FP_ENROLL_RETRY_CENTER_FINGER:
				printf("Didn't catch that, please center your finger on the sensor and try again.\n");
				snprintf(str, sizeof(str), "enroll.php|%d|Please center your finger and try again", stage);
                                updategui(str);
				break;
			case FP_ENROLL_RETRY_REMOVE_FINGER:
				printf("Scan failed, please remove your finger and then try again.\n");
				snprintf(str, sizeof(str), "enroll.php|%d|Please remove your finger and try again", stage);
                                updategui(str);
				break;
		}
	} while (r != FP_ENROLL_COMPLETE);

	if (!enrolled_print) {
		fprintf(stderr, "Enroll complete but no print?\n");
	}

	printf("Enrollment completed!\n\n");

	unsigned char *buf;
	size_t buflen = fp_print_data_get_data(enrolled_print,&buf);
	FILE *fpadmin = fopen("../fingers/admin", "w");
	if(fpadmin == NULL) {
		printf("Failed writing admin finger");
		return 0;
	}
	fwrite(buf, sizeof(unsigned char), buflen, fpadmin);
	fclose(fpadmin);
	free(buf);

	fp_print_data_free(enrolled_print);
	fp_dev_close(dev);
	fp_dscv_devs_free(devs);
	fp_exit();
	printf("\n");
	return 0;
}
